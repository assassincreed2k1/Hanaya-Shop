@php
    $total = 0;
    if (isset($selectedItems) && is_array($selectedItems)) {
        foreach ($selectedItems as $item) {
            $total += $item['subtotal'] ?? 0;
        }
    }
    $defaultMethod =
        isset($paymentMethods) && count($paymentMethods) > 0
            ? ucfirst(str_replace('_', ' ', $paymentMethods[0]))
            : 'Không có phương thức';

@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Thanh toán') }}
        </h2>
    </x-slot>
    <x-alert />

    <div class="max-w-4xl mx-auto py-12 px-2 sm:px-4 space-y-8 sm:space-y-10">

        {{-- Address Information --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Address Information</h3>
            <h4 class="text-gray-700 leading-relaxed mb-4">
                <span class="block font-medium">{{ $userName }}</span>

                @if ($firstAddress)
                    <span id="selected-phone" class="block text-sm text-gray-600">{{ $firstAddress->phone_number }}</span>
                    <span id="selected-address" class="block text-sm text-gray-600">
                        {{ $firstAddress->address }}
                    </span>
                @else
                    <span id="selected-phone" class="block text-sm text-gray-600">NA</span>
                    <span id="selected-address" class="block text-sm text-gray-600">
                        NA
                    </span>
                @endif
            </h4>

            <button id="toggle-address-list"
                class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-pink-700 transition">
                Change Address
            </button>

            <button id="open-address-form"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-pink-700 transition">
                Add Address
            </button>
        </div>

        {{-- Product List --}}
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 overflow-x-auto">
            @if (count($selectedItems) > 0)
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Images</th>
                            <th class="py-2 px-4 border-b">Product Name</th>
                            <th class="py-2 px-4 border-b">Price</th>
                            <th class="py-2 px-4 border-b">Quantity</th>
                            <th class="py-2 px-4 border-b">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach ($selectedItems as $item)
                            @php $total += $item['subtotal']; @endphp
                            <tr>
                                <td class="py-2 px-4 border-b">
                                    <img src="{{ $item['image'] }}"
                                        class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded">
                                </td>
                                <td class="py-2 px-4 border-b">{{ $item['name'] }}</td>
                                <td class="py-2 px-4 border-b">{{ number_format($item['price'], 0, ',', '.') }} USD</td>
                                <td class="py-2 px-4 border-b">{{ $item['quantity'] }}</td>
                                <td class="py-2 px-4 border-b">{{ number_format($item['subtotal'], 0, ',', '.') }} USD
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-right font-bold py-2 px-4">Total:</td>
                            <td class="font-bold py-2 px-4">{{ number_format($total, 0, ',', '.') }} USD</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p>No products selected for checkout.</p>
            @endif
        </div>

        {{-- Payment --}}
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Payment Method:</h3>
                    <h4 class="text-gray-700" id="current-method">{{ $defaultMethod }}</h4>
                </div>
                <button id="change-method-btn" type="button"
                    class="bg-pink-600 text-white px-3 sm:px-4 py-2 rounded hover:bg-pink-700 transition whitespace-nowrap w-full sm:w-auto">
                    Change
                </button>
            </div>

            <div>
                <div class="flex items-center justify-between flex-wrap gap-2 mb-4 text-xs sm:text-base">
                    <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-2">Subtotal:</h3>
                    <p class="text-xl font-bold text-pink-600">{{ number_format($total, 0, ',', '.') }}₫</p>
                </div>
                <div class="flex items-center justify-between flex-wrap gap-2 mb-4 text-xs sm:text-base">
                    <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-2">Shipping Fee:</h3>
                    <p class="text-xl font-bold text-pink-600">8 USD</p>
                </div>
                <div class="flex items-center justify-between flex-wrap gap-2 mb-4 text-xs sm:text-base">
                    <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-2">Total Payment:</h3>
                    <p class="text-xl font-bold text-pink-800">{{ number_format($total + 8, 0, ',', '.') }} USD</p>
                </div>
            </div>

            <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method_input"
                    value="{{ $paymentMethods[0] ?? 'cash_on_delivery' }}">
                <input type="hidden" name="payment_data" id="payment_data" value="{}">

                <input type="hidden" name="selected_items_json" value='@json($selectedItems)'>

                <input type="hidden" name="address_id" id="selected_address_id" value="{{ $firstAddress->id ?? '' }}">

                <h3 class="text-lg font-semibold text-gray-800 mb-2">Note:</h3>
                <textarea name="note" class="w-full p-2 border rounded text-xs sm:text-sm mb-4" rows="4"
                    placeholder="Enter your note...">{{ old('note') }}</textarea>

                <div class="mt-6">
                    <div id="payment-methods-container" class="space-y-4">
                        <x-payment.credit-card />
                        <x-payment.paypal />
                        <x-payment.cash-on-delivery />
                    </div>
                </div>

                <div class="mt-6">
                    <button id="direct-submit-btn" type="submit"
                        class="bg-orange-600 text-white px-3 sm:px-4 py-2 rounded w-full sm:w-auto">
                        Place Order
                    </button>
                    <p class="text-xs text-gray-500 mt-2">Or select a payment method from the options above</p>
                </div>
            </form>




        </div>
    </div>

    <!-- Payment method selection modal -->
    <div id="method-selection" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center h-full">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Select Payment Method</h3>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach ($paymentMethods as $method)
                        <button type="button"
                            class="method-option px-4 py-2 border rounded-lg hover:bg-pink-50 transition text-sm sm:text-base flex items-center"
                            data-method="{{ $method }}">
                            @if ($method === 'credit_card')
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </span>
                            @elseif($method === 'paypal')
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="#00457c">
                                        <path
                                            d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.384a.99.99 0 0 1 .984-.822h6.712c3.16 0 4.23 1.542 3.891 3.825-.482 3.253-2.608 4.218-5.257 4.218h-.32a.654.654 0 0 0-.648.553l-.784 4.987a.841.841 0 0 1-.837.697H7.076v4.495z" />
                                        <path
                                            d="M14.588 7.643a.64.64 0 0 0 .634-.74L13.114 3.74a.64.64 0 0 0-.633-.74H6.768a.64.64 0 0 0-.633.74l2.107 13.165a.64.64 0 0 0 .633.74h5.712a.64.64 0 0 0 .634-.74l-.784-4.987a.64.64 0 0 0-.633-.74h-1.237a.64.64 0 0 1-.634-.74l.123-.772a.64.64 0 0 1 .634-.74h2.498v-.273z" />
                                    </svg>
                                </span>
                            @elseif($method === 'cash_on_delivery')
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </span>
                            @endif
                            {{ ucfirst(str_replace('_', ' ', $method)) }}
                        </button>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <button id="close-method-selection"
                        class="mt-4 text-sm text-gray-500 hover:underline hover:text-gray-700">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form -->
    <div id="address-form-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center h-full">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Add Shipping Address</h3>

                <div class="mb-4">
                    <label class="block mb-1 text-gray-700">Phone Number</label>
                    <input id="phone_number" type="text" class="w-full border rounded px-3 py-2"
                        placeholder="Enter phone number" required>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-gray-700">City/Province</label>
                    <select id="province" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Select Province --</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-gray-700">District</label>
                    <select id="district" class="w-full border rounded px-3 py-2" disabled required>
                        <option value="">-- Select District --</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-gray-700">Ward</label>
                    <select id="ward" class="w-full border rounded px-3 py-2" disabled required>
                        <option value="">-- Select Ward --</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-gray-700">Detailed Address</label>
                    <input id="address_detail" type="text" class="w-full border rounded px-3 py-2"
                        placeholder="House number, street name..." required>
                </div>

                <div class="flex justify-between items-center">
                    <button id="save-address" type="button"
                        class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700 transition">
                        Save Address
                    </button>
                    <button id="close-address-form" class="text-gray-600 hover:underline">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Address List -->
    <div id="address-list-container" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center h-full">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-hidden relative">
                <h3 class="text-xl font-semibold text-center mb-4 pt-6">Address List</h3>

                <!-- Scrollable area for the address list -->
                <div id="address-list" class="space-y-4 px-6 overflow-y-auto max-h-[60vh]">
                    @foreach ($addresses as $address)
                        <a href="#" class="address-item" data-id="{{ $address->id }}"
                            data-phone="{{ $address->phone_number }}" data-address="{{ $address->address }}">
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 shadow-sm">
                                <p class="text-gray-800"><strong>Phone:</strong> {{ $address->phone_number }}</p>
                                <p class="text-gray-800"><strong>Address:</strong> {{ $address->address }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center px-6 pb-6">
                    <button
                        class="close-address-list px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============= PAYMENT METHOD LOGIC =============
            const btnChangeMethod = document.getElementById('change-method-btn');
            const methodBox = document.getElementById('method-selection');
            const currentMethod = document.getElementById('current-method');
            const closeMethodBtn = document.getElementById('close-method-selection');
            const paymentForms = document.querySelectorAll('.payment-method-form');
            const defaultMethod = '{{ $paymentMethods[0] ?? 'cash_on_delivery' }}';

            // Function to show the appropriate payment form
            function showPaymentForm(method) {
                // Hide all payment forms
                paymentForms.forEach(form => {
                    if (window.Alpine) {
                        const alpineData = window.Alpine.$data(form);
                        if (alpineData && typeof alpineData.isVisible !== 'undefined') {
                            alpineData.isVisible = false;
                        }
                    }
                });

                // Show the selected payment form
                const formId = `${method.replace(/_/g, '-')}-form`;
                const selectedForm = document.getElementById(formId);
                if (selectedForm && window.Alpine) {
                    const alpineData = window.Alpine.$data(selectedForm);
                    if (alpineData && typeof alpineData.isVisible !== 'undefined') {
                        alpineData.isVisible = true;
                    }
                }
            }

            // Show the initial payment form based on the default method
            showPaymentForm(defaultMethod);

            // Handle direct submit button
            document.getElementById('direct-submit-btn').addEventListener('click', function(e) {
                e.preventDefault();
                const selectedMethod = document.getElementById('payment_method_input').value;

                // If no form is visible or COD is selected, submit the form directly
                if (selectedMethod === 'cash_on_delivery') {
                    document.getElementById('payment_data').value = JSON.stringify({
                        terms_accepted: true
                    });
                    document.getElementById('checkout-form').submit();
                } else {
                    // Otherwise show the appropriate payment form
                    showPaymentForm(selectedMethod);
                    // Scroll to the payment form
                    document.querySelector(`#${selectedMethod.replace(/_/g, '-')}-form`).scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });

            // Show payment method selection modal
            btnChangeMethod.addEventListener('click', function() {
                methodBox.classList.remove('hidden');
            });

            // Close modal when a payment method is selected
            document.querySelectorAll('.method-option').forEach(button => {
                button.addEventListener('click', function() {
                    const selected = this.dataset.method;
                    document.getElementById('payment_method_input').value = selected;
                    currentMethod.textContent = selected.charAt(0).toUpperCase() + selected.slice(1)
                        .replaceAll('_', ' ');
                    methodBox.classList.add('hidden');

                    // Show the selected payment form
                    showPaymentForm(selected);
                });
            });

            // Close modal when cancel button is pressed
            closeMethodBtn.addEventListener('click', function() {
                methodBox.classList.add('hidden');
            });
        });

        // ============= ADDRESS LOGIC - SEPARATE LISTENER =============
        // Tách thành listener riêng để tránh xung đột với các listener khác
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Address logic loaded");
            
            // Các phần tử DOM address
            const openAddressFormBtn = document.getElementById('open-address-form');
            const toggleAddressListBtn = document.getElementById('toggle-address-list');
            const addressFormModal = document.getElementById('address-form-modal');
            const addressListContainer = document.getElementById('address-list-container');
            const closeAddressFormBtn = document.getElementById('close-address-form');
            
            // Debug - kiểm tra các phần tử có tồn tại không
            console.log("openAddressFormBtn:", openAddressFormBtn);
            console.log("toggleAddressListBtn:", toggleAddressListBtn);
            console.log("addressListContainer:", addressListContainer);
            
            // Mở form thêm địa chỉ mới
            if (openAddressFormBtn) {
                openAddressFormBtn.addEventListener('click', function(e) {
                    console.log("Open address form clicked");
                    e.preventDefault();
                    addressFormModal.classList.remove('hidden');
                    
                    // Tải dữ liệu tỉnh/thành nếu cần
                    const provinceSelect = document.getElementById('province');
                    if (provinceSelect && provinceSelect.options.length === 1) {
                        fetch('https://provinces.open-api.vn/api/p/')
                            .then(res => res.json())
                            .then(data => {
                                data.forEach(p => {
                                    let opt = document.createElement('option');
                                    opt.value = p.code;
                                    opt.textContent = p.name;
                                    provinceSelect.appendChild(opt);
                                });
                            })
                            .catch(err => console.error("Error loading provinces:", err));
                    }
                });
            }

            // Mở danh sách địa chỉ
            if (toggleAddressListBtn) {
                toggleAddressListBtn.addEventListener('click', function(e) {
                    console.log("Toggle address list clicked");
                    e.preventDefault();
                    addressListContainer.classList.toggle('hidden');
                });
            }
            
            // Đóng form thêm địa chỉ
            if (closeAddressFormBtn) {
                closeAddressFormBtn.addEventListener('click', function() {
                    addressFormModal.classList.add('hidden');
                });
            }
            
            // Đóng danh sách địa chỉ
            document.querySelectorAll('.close-address-list').forEach(button => {
                button.addEventListener('click', function() {
                    addressListContainer.classList.add('hidden');
                });
            });
            
            // Chọn địa chỉ từ danh sách
            document.querySelectorAll('.address-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const phone = this.dataset.phone;
                    const address = this.dataset.address;
                    
                    console.log("Selected address:", id, phone, address);
                    
                    // Cập nhật thông tin
                    document.getElementById('selected_address_id').value = id;
                    document.getElementById('selected-phone').textContent = phone;
                    document.getElementById('selected-address').innerHTML = address.replace(/\n/g, '<br>');
                    
                    // Ẩn danh sách địa chỉ
                    addressListContainer.classList.add('hidden');
                });
            });
            
            // Xử lý thay đổi tỉnh/thành
            const provinceSelect = document.getElementById('province');
            if (provinceSelect) {
                provinceSelect.addEventListener('change', function() {
                    const code = this.value;
                    const districtSelect = document.getElementById('district');
                    const wardSelect = document.getElementById('ward');
                    
                    districtSelect.innerHTML = '<option value="">-- Select District --</option>';
                    wardSelect.innerHTML = '<option value="">-- Select Ward --</option>';
                    wardSelect.disabled = true;
                    
                    if (!code) {
                        districtSelect.disabled = true;
                        return;
                    }
                    
                    districtSelect.disabled = false;
                    fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.districts.forEach(d => {
                                const opt = document.createElement('option');
                                opt.value = d.code;
                                opt.textContent = d.name;
                                districtSelect.appendChild(opt);
                            });
                        });
                });
            }
            
            // Xử lý thay đổi quận/huyện
            const districtSelect = document.getElementById('district');
            if (districtSelect) {
                districtSelect.addEventListener('change', function() {
                    const code = this.value;
                    const wardSelect = document.getElementById('ward');
                    
                    wardSelect.innerHTML = '<option value="">-- Select Ward --</option>';
                    
                    if (!code) {
                        wardSelect.disabled = true;
                        return;
                    }
                    
                    wardSelect.disabled = false;
                    fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.wards.forEach(w => {
                                const opt = document.createElement('option');
                                opt.value = w.code;
                                opt.textContent = w.name;
                                wardSelect.appendChild(opt);
                            });
                        });
                });
            }
            
            // Lưu địa chỉ mới
            const saveAddressBtn = document.getElementById('save-address');
            if (saveAddressBtn) {
                saveAddressBtn.addEventListener('click', function() {
                    const phone = document.getElementById('phone_number').value;
                    const province = provinceSelect.selectedOptions[0]?.text || '';
                    const district = districtSelect.selectedOptions[0]?.text || '';
                    const ward = document.getElementById('ward').selectedOptions[0]?.text || '';
                    const detail = document.getElementById('address_detail').value;
                    
                    const fullAddress = `${detail}, ${ward}, ${district}, ${province}`;
                    
                    if (!phone || !province || !district || !ward || !detail) {
                        alert("Please fill in all the information.");
                        return;
                    }
                    
                    fetch('{{ route('addresses.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            phone_number: phone,
                            address: fullAddress
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const id = data.address?.id;
                        const phone = data.address?.phone_number;
                        const addressText = data.address?.address;
                        
                        const a = document.createElement('a');
                        a.href = '#';
                        a.className = 'address-item';
                        a.dataset.id = id;
                        a.dataset.phone = phone;
                        a.dataset.address = addressText;
                        
                        a.innerHTML = `
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 shadow-sm">
                                <p class="text-gray-800"><strong>Phone:</strong> ${phone}</p>
                                <p class="text-gray-800"><strong>Address:</strong> ${addressText}</p>
                            </div>
                        `;
                        
                        const addressList = document.getElementById('address-list');
                        addressList.appendChild(a);
                        
                        // Thêm event listener cho địa chỉ mới
                        a.addEventListener('click', function(e) {
                            e.preventDefault();
                            document.getElementById('selected_address_id').value = id;
                            document.getElementById('selected-phone').textContent = phone;
                            document.getElementById('selected-address').innerHTML = addressText
                                .replace(/\n/g, '<br>');
                            addressListContainer.classList.add('hidden');
                        });
                        
                        // Ẩn modal và reset form
                        addressFormModal.classList.add('hidden');
                        document.getElementById('phone_number').value = '';
                        document.getElementById('address_detail').value = '';
                        
                        console.log("Address saved successfully");
                    })
                    .catch(err => {
                        console.error("Error saving address:", err);
                        alert("Error: " + err.message);
                    });
                });
            }
        });
    </script>


</x-app-layout>
