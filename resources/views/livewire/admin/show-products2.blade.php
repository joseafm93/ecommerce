<div>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-600 leading-tight">
                Lista de productos 2 (duplicación)
            </h2>
        </div>
    </x-slot>

    <x-table-responsive-2>

        <div class="px-6 py-4">
            <x-jet-input dusk="adminSearch" class="w-full" wire:model="search" type="text"
                         placeholder="Introduzca el nombre del producto a buscar" />
        </div>

        @if ($products->count())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Categoría
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subcategoría
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Marca
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha de creación
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Stock
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Color
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Talla
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Precio
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Editar</span>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 object-cover">
                                    <img class="h-10 w-10 rounded-full"
                                         src="{{ $product->images->count() ? Storage::url($product->images->first()->url) : 'img/default.jpg' }}"
                                         alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $product->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        jane.cooper@example.com
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->subcategory->category->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->subcategory->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->brand->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->created_at }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->stock }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($product->subcategory->size)
                                <div class="border-b border-gray-200 mb-2">
                                    <div class="text-sm text-gray-900 mr-2 font-semibold">Colores:</div>
                                    <div class="flex flex-wrap text-sm text-gray-900">
                                        @foreach($product->sizes->pluck('colors')->collapse()->pluck('name')->unique() as $uniqueColor)
                                            <span class="mr-1 {{ $loop->first ? 'mr-0' : '' }}">
                                                    {{ $loop->first ? '' : ',' }}</span>
                                            <span>{{__(ucfirst($uniqueColor))}}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @foreach($product->sizes as $size )
                                    <div
                                        class="flex justify-between text-sm font-semibold text-gray-900 {{ $loop->first ? '' : 'pt-2' }}">
                                        <span>{{ $size->name }}</span>
                                        <span>[{{ $size->colors->sum('pivot.quantity') }}]</span>
                                    </div>
                                    @foreach($size->colors as $color)
                                        <div class="flex justify-between">
                                                <span class="text-sm text-gray-900 mr-2">
                                                    {{__(ucfirst($color->name))}}</span>
                                            <span class="text-sm text-gray-900">
                                                    [{{ $color->pivot->quantity }}]
                                                </span>
                                        </div>
                                    @endforeach
                                @endforeach
                            @elseif($product->subcategory->color)
                                @foreach($product->colors as $color)
                                    <div class="flex justify-between">
                                            <span
                                                class="text-sm text-gray-900 mr-2">{{__(ucfirst($color->name))}}</span>
                                        <span
                                            class="text-sm text-gray-900">[{{ $color->pivot->quantity }}]</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-900">-</span>

                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($product->subcategory->size)
                                @foreach($product->sizes as  $size)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-900 pb-1 mr-1"> {{ $size->name }}</span>
                                        <span class="text-sm text-gray-900 pb-1">[{{ $size->colors->sum('pivot.quantity') }}]</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-900">-</span>
                            @endif

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $product->status == 1 ? 'red' : 'green' }}-100 text-{{ $product->status == 1 ? 'red' : 'green' }}-800">
                                    {{ $product->status == 1 ? 'Borrador' : 'Publicado' }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->price }} &euro;
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-indigo-600 hover:text-indigo-900">Editar</a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

        @else
            <div class="px-6 py-4">
                No existen productos coincidentes
            </div>
        @endif

        @if ($products->hasPages())
            <div class="px-6 py-4">
                {{ $products->links() }}
            </div>
        @endif
    </x-table-responsive-2>
</div>
