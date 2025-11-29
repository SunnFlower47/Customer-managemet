@props(['type' => 'table', 'rows' => 5])

@if($type === 'table')
    <!-- Table Skeleton -->
    <div class="animate-pulse">
        <div class="hidden lg:block overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-20"></div></th>
                        <th class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-24"></div></th>
                        <th class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-20"></div></th>
                        <th class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-24"></div></th>
                        <th class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-16"></div></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 0; $i < $rows; $i++)
                    <tr>
                        <td class="px-4 py-4"><div class="h-4 bg-gray-200 rounded w-32"></div></td>
                        <td class="px-4 py-4"><div class="h-4 bg-gray-200 rounded w-28"></div></td>
                        <td class="px-4 py-4"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
                        <td class="px-4 py-4"><div class="h-4 bg-gray-200 rounded w-20"></div></td>
                        <td class="px-4 py-4"><div class="h-8 bg-gray-200 rounded w-24"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <!-- Mobile Cards Skeleton -->
        <div class="lg:hidden space-y-3">
            @for($i = 0; $i < $rows; $i++)
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-gray-200 rounded-xl"></div>
                    <div class="flex-1">
                        <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
                <div class="space-y-2 mt-3">
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
@elseif($type === 'card')
    <!-- Card Skeleton -->
    <div class="animate-pulse">
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
            <div class="space-y-3">
                <div class="h-4 bg-gray-200 rounded w-full"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                <div class="h-4 bg-gray-200 rounded w-4/6"></div>
            </div>
        </div>
    </div>
@elseif($type === 'stats')
    <!-- Stats Cards Skeleton -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @for($i = 0; $i < 4; $i++)
        <div class="animate-pulse bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 rounded w-24 mb-3"></div>
                    <div class="h-8 bg-gray-200 rounded w-32 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-20"></div>
                </div>
                <div class="w-12 h-12 bg-gray-200 rounded-xl"></div>
            </div>
        </div>
        @endfor
    </div>
@endif

