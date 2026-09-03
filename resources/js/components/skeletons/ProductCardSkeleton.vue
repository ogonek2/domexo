<template>
    <article class="flex h-full animate-pulse flex-col border border-gray-200 bg-white">
        <div class="aspect-square border-b border-gray-100 bg-gray-100"></div>
        <div class="flex flex-1 flex-col gap-2 p-3">
            <div class="h-3 w-full bg-gray-100"></div>
            <div class="h-3 w-4/5 bg-gray-100"></div>
            <div class="h-2.5 w-1/2 bg-gray-100"></div>
            <div class="mt-1 h-4 w-2/5 bg-gray-100"></div>
            <div class="mt-2 h-8 w-full bg-gray-100"></div>
            <div class="h-9 w-full bg-gray-200"></div>
        </div>
    </article>
</template>

<script>
export default { name: 'ProductCardSkeleton' };
</script>
