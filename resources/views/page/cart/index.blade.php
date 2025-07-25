{{-- filepath: resources/views/cart/index.blade.php --}}
<x-app-layout>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (count($cart) > 0)
                <form id="checkout-form" method="POST" action="{{ route('checkout.preview') }}">
                    @csrf
                    <input type="hidden" name="selected_items_json" id="selected_items_json">
                    <!-- Desktop Table -->
                    <div class="hidden md:block">
                        <h2 class="text-2xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14-4H9m4 8H7m6 4v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002-2z"></path>
                            </svg>
                            Cart
                        </h2>
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mt-8">

                            <table class="min-w-full">
                                <thead class="bg-gradient-to-r from-pink-50 to-purple-50">
                                    <tr>
                                        <th class="py-3 px-4 text-center">
                                            <input type="checkbox" id="select-all" title="Chọn tất cả">
                                        </th>
                                        <th class="py-3 px-4">Ảnh</th>
                                        <th class="py-3 px-4">Tên sản phẩm</th>
                                        <th class="py-3 px-4">Giá</th>
                                        <th class="py-3 px-4">Số lượng</th>
                                        <th class="py-3 px-4">Thành tiền</th>
                                        <th class="py-3 px-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($cart as $id => $item)
                                        @php $total += $item['price'] * $item['quantity']; @endphp
                                        <tr class="hover:bg-pink-50 transition-all">
                                            <td class="py-3 px-4 text-center align-middle">
                                                <input type="checkbox" name="cart_ids[]" value="{{ $id }}"
                                                    class="cart-checkbox accent-pink-500 w-5 h-5"
                                                    data-price="{{ $item['price'] * $item['quantity'] }}"
                                                    data-id="{{ $id }}"
                                                    data-product-id="{{ $item['product_id'] }}">
                                            </td>
                                            <td class="py-3 px-4 align-middle">
                                                <div
                                                    class="w-16 h-16 rounded-lg overflow-hidden border-2 border-pink-200 bg-gray-100 flex items-center justify-center">
                                                    <img src="{{ $item['image_url'] && file_exists(public_path('images/products/' . $item['image_url']))
                                                        ? asset('images/products/' . $item['image_url'])
                                                        : asset('images/no-image.png') }}"
                                                        alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 align-middle font-semibold text-gray-900">
                                                {{ $item['name'] }}</td>
                                            <td class="py-3 px-4 align-middle text-pink-600 font-bold">
                                                {{ number_format($item['price'], 0, ',', '.') }} USD</td>
                                            <td class="py-3 px-4 align-middle text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button"
                                                        class="btn-decrease bg-gray-200 hover:bg-pink-100 text-pink-600 px-2 rounded transition"
                                                        data-id="{{ $id }}">−</button>
                                                    <input type="number" min="1"
                                                        class="quantity-input w-[80px] text-center border rounded focus:ring-pink-500"
                                                        value="{{ $item['quantity'] }}" data-id="{{ $id }}"
                                                        data-price="{{ $item['price'] }}"
                                                        data-total="{{ $item['price'] * $item['quantity'] }}"
                                                        data-stock="{{ $item['product_quantity'] }}">
                                                    <button type="button"
                                                        class="btn-increase bg-gray-200 hover:bg-pink-100 text-pink-600 px-2 rounded transition"
                                                        data-id="{{ $id }}">+</button>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 align-middle item-total font-bold text-purple-700"
                                                data-id="{{ $id }}">
                                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                USD
                                            </td>
                                            <td class="py-3 px-4 align-middle">
                                                <a href="{{ route('cart.remove', $id) }}"
                                                    class="text-red-600 hover:underline font-medium">Xóa</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-right font-bold py-3 px-4 text-lg text-gray-700">
                                            Tổng cộng:</td>
                                        <td colspan="2" class="font-bold py-3 px-4 text-lg text-pink-600"
                                            id="totalPrice">0 USD</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-8 text-right">
                            <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Thanh toán
                            </button>
                        </div>
                    </div>
                    <!-- Mobile Card List -->
                    <div class="md:hidden space-y-6 mt-8">
                        <div
                            class="bg-white rounded-xl shadow-lg p-4 flex items-center gap-3 border-l-4 border-pink-500">
                            <input type="checkbox" id="select-all-mobile" title="Chọn tất cả"
                                class="accent-pink-500 w-5 h-5">
                            <span class="font-semibold text-gray-700">Chọn tất cả</span>
                        </div>
                        @foreach ($cart as $id => $item)
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 flex flex-col gap-3">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="cart_ids[]" value="{{ $id }}"
                                        class="cart-checkbox accent-pink-500 w-5 h-5"
                                        data-price="{{ $item['price'] * $item['quantity'] }}"
                                        data-id="{{ $id }}" data-product-id="{{ $item['product_id'] }}">
                                    <div
                                        class="w-20 h-20 rounded-lg overflow-hidden border-2 border-pink-200 bg-gray-100 flex items-center justify-center">
                                        <img src="{{ $item['image_url'] && file_exists(public_path('images/products/' . $item['image_url']))
                                            ? asset('images/products/' . $item['image_url'])
                                            : asset('images/no-image.png') }}"
                                            alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $item['name'] }}</div>
                                        <div class="text-pink-600 font-bold">
                                            {{ number_format($item['price'], 0, ',', '.') }} USD</div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            class="btn-decrease bg-gray-200 hover:bg-pink-100 text-pink-600 px-2 rounded transition"
                                            data-id="{{ $id }}">−</button>
                                        <input type="number" min="1"
                                            class="quantity-input w-[60px] text-center border rounded focus:ring-pink-500"
                                            value="{{ $item['quantity'] }}" data-id="{{ $id }}"
                                            data-price="{{ $item['price'] }}"
                                            data-total="{{ $item['price'] * $item['quantity'] }}">
                                        <button type="button"
                                            class="btn-increase bg-gray-200 hover:bg-pink-100 text-pink-600 px-2 rounded transition"
                                            data-id="{{ $id }}">+</button>
                                    </div>
                                    <div class="item-total font-bold text-purple-700" data-id="{{ $id }}">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} USD
                                    </div>
                                    <a href="{{ route('cart.remove', $id) }}"
                                        class="text-red-600 hover:underline font-medium">Xóa</a>
                                </div>
                            </div>
                        @endforeach
                        <div
                            class="bg-white rounded-xl shadow-lg p-4 flex justify-between items-center font-bold border-l-4 border-purple-500">
                            <span class="text-gray-700">Tổng cộng:</span>
                            <span id="totalPrice" class="text-pink-600">0 USD</span>
                        </div>
                        <div class="text-right mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Thanh toán
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Giỏ hàng của bạn đang trống</h3>
                    <p class="text-gray-500 mb-6">Bạn chưa thêm sản phẩm nào vào giỏ hàng. Hãy mua sắm ngay!</p>
                    <a href="{{ route('user.products.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const totalPriceEl = document.getElementById('totalPrice');
            const selectAllCheckbox = document.getElementById('select-all');
            const selectAllMobile = document.getElementById('select-all-mobile');
            const checkoutForm = document.getElementById('checkout-form');

            function formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + ' USD';
            }

            function updateTotal() {
                let total = 0;
                const checkedIds = new Set();
                document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
                    const id = cb.dataset.id;
                    if (checkedIds.has(id)) return;
                    checkedIds.add(id);
                    const qtyInput = document.querySelector(`.quantity-input[data-id="${id}"]`);
                    const price = parseFloat(qtyInput.dataset.price);
                    const quantity = parseInt(qtyInput.value);
                    total += price * quantity;
                });
                // Cập nhật cả tổng desktop và mobile
                document.querySelectorAll('#totalPrice').forEach(el => {
                    el.textContent = formatCurrency(total);
                });
            }

            function updateCheckboxPrice(id, newTotal) {
                const cb = document.querySelector(`.cart-checkbox[data-id="${id}"]`);
                if (cb) {
                    cb.dataset.price = newTotal;
                }
            }

            function updateItemTotal(id, quantity, price) {
                const totalCell = document.querySelector(`.item-total[data-id="${id}"]`);
                if (totalCell) {
                    const total = quantity * price;
                    totalCell.textContent = formatCurrency(total);
                }
            }

            // Tăng giảm số lượng
            document.querySelectorAll('.btn-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                    let quantity = parseInt(input.value);
                    quantity++;
                    input.value = quantity;
                    updateCheckboxPrice(id, quantity * parseFloat(input.dataset.price));
                    updateItemTotal(id, quantity, parseFloat(input.dataset.price));
                    updateTotal();
                });
            });

            document.querySelectorAll('.btn-decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                    let quantity = parseInt(input.value);
                    if (quantity > 1) {
                        quantity--;
                        input.value = quantity;
                        updateCheckboxPrice(id, quantity * parseFloat(input.dataset.price));
                        updateItemTotal(id, quantity, parseFloat(input.dataset.price));
                        updateTotal();
                    }
                });
            });

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('input', function() {
                    const id = this.dataset.id;
                    const quantity = parseInt(this.value);
                    if (quantity >= 1) {
                        updateCheckboxPrice(id, quantity * parseFloat(this.dataset.price));
                        updateItemTotal(id, quantity, parseFloat(this.dataset.price));
                        updateTotal();
                    }
                });
            });

            // Checkbox chọn tất cả
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    document.querySelectorAll('.cart-checkbox').forEach(cb => {
                        cb.checked = isChecked;
                    });
                    if (selectAllMobile) selectAllMobile.checked = isChecked;
                    updateTotal();
                });
            }
            if (selectAllMobile) {
                selectAllMobile.addEventListener('change', function() {
                    const isChecked = this.checked;
                    document.querySelectorAll('.cart-checkbox').forEach(cb => {
                        cb.checked = isChecked;
                    });
                    if (selectAllCheckbox) selectAllCheckbox.checked = isChecked;
                    updateTotal();
                });
            }

            // Khi chọn 1 checkbox thì kiểm tra lại trạng thái chọn tất cả
            document.querySelectorAll('.cart-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const allCheckboxes = document.querySelectorAll('.cart-checkbox');
                    const allChecked = [...allCheckboxes].every(c => c.checked);
                    if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
                    if (selectAllMobile) selectAllMobile.checked = allChecked;
                    updateTotal();
                });
            });

            // Gửi form
            checkoutForm.addEventListener('submit', function(e) {
                const checkedCheckboxes = document.querySelectorAll('.cart-checkbox:checked');
                const selectedItems = [];
                const checkedIds = new Set();
                document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
                    const id = cb.dataset.id;

                    if (checkedIds.has(id)) return; // Nếu đã lấy rồi thì bỏ qua
                    checkedIds.add(id);
                    // Tìm row cho desktop hoặc card cho mobile
                    let row = cb.closest('tr');
                    if (!row) {
                        row = cb.closest('.bg-white');
                    }
                    const image = row.querySelector('img').getAttribute('src');
                    // Desktop: tên ở td thứ 3, Mobile: lấy .font-semibold
                    let name = '';
                    const nameTd = row.querySelector('td:nth-child(3)');
                    if (nameTd) {
                        name = nameTd.textContent.trim();
                    } else {
                        const nameDiv = row.querySelector('.font-semibold');
                        name = nameDiv ? nameDiv.textContent.trim() : '';
                    }
                    const price = parseFloat(row.querySelector('.quantity-input').dataset.price);
                    const quantity = parseInt(row.querySelector('.quantity-input').value);
                    const subtotal = price * quantity;
                    const stock_quantity = parseInt(row.querySelector('.quantity-input').dataset
                        .stock);

                    selectedItems.push({
                        id: cb.dataset.productId, // Lấy đúng product_id từ checkbox
                        image,
                        name,
                        price,
                        quantity,
                        subtotal,
                        stock_quantity
                    });
                });
                document.getElementById('selected_items_json').value = JSON.stringify(selectedItems);
            });

            updateTotal();
        });
    </script>


</x-app-layout>
