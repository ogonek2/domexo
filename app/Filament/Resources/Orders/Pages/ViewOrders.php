<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrdersResource;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Orders;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ViewOrders extends ViewRecord
{
    protected static string $resource = OrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Изменить статус')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->fillForm(fn (Orders $record): array => [
                    'status' => $record->status ?: Orders::STATUS_NEW,
                    'tracking_number' => $record->tracking_number,
                    'notify_customer' => true,
                ])
                ->form([
                    Select::make('status')
                        ->label('Статус')
                        ->options(Orders::STATUSES)
                        ->required()
                        ->live(),
                    TextInput::make('tracking_number')
                        ->label('Номер накладной')
                        ->maxLength(120)
                        ->helperText('Обязателен для статусов «Отправлено» и «Создан на почте»')
                        ->required(fn (Get $get): bool => in_array($get('status'), Orders::STATUSES_WITH_TRACKING, true))
                        ->visible(fn (Get $get): bool => in_array($get('status'), Orders::STATUSES_WITH_TRACKING, true)
                            || filled($get('tracking_number'))),
                    Toggle::make('notify_customer')
                        ->label('Отправить письмо клиенту')
                        ->default(true)
                        ->helperText('Для статусов с отправкой письмо содержит номер накладной'),
                ])
                ->action(function (array $data, Orders $record): void {
                    $oldStatus = $record->status;
                    $newStatus = $data['status'];
                    $tracking = trim((string) ($data['tracking_number'] ?? ''));

                    if (in_array($newStatus, Orders::STATUSES_WITH_TRACKING, true) && $tracking === '') {
                        Notification::make()
                            ->title('Укажите номер накладной')
                            ->danger()
                            ->send();

                        return;
                    }

                    // status / tracking_number — обычные поля (не encryptable)
                    $record->forceFill([
                        'status' => $newStatus,
                        'tracking_number' => $tracking !== '' ? $tracking : null,
                    ])->save();

                    $shouldNotify = (bool) ($data['notify_customer'] ?? false);
                    $email = trim((string) $record->email);

                    if ($shouldNotify && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($email)->send(new OrderStatusUpdatedMail(
                                $record->fresh(),
                                Orders::STATUSES[$newStatus] ?? $newStatus,
                            ));
                            Notification::make()
                                ->title('Статус обновлён, письмо отправлено')
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            Log::error('Order status mail failed: '.$e->getMessage());
                            Notification::make()
                                ->title('Статус обновлён, но письмо не отправилось')
                                ->body($e->getMessage())
                                ->warning()
                                ->send();
                        }
                    } else {
                        Notification::make()
                            ->title($oldStatus === $newStatus ? 'Данные сохранены' : 'Статус обновлён')
                            ->success()
                            ->send();
                    }
                }),

            Action::make('printOrder')
                ->label('Печатать заказ')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (Orders $record): string => route('admin.orders.print', $record))
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }
}
