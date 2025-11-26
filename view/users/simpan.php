<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link rel="icon" href="../assets/img/Nanocomp.png">
    <link rel="stylesheet" href="/../../src/output.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body class="">
<div class="relative isolate min-h-screen">
  <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
    <div class="h-[60vh] w-[60vh] rounded-full bg-gradient-to-br absolute -top-32 -left-32 from-red-200 via-rose-200
        to-pink-300 opacity-20 blur-2xl dark:opacity-0"></div>
    <div class="h-[40vh] w-[50vh] rounded-full bg-gradient-to-tr absolute -bottom-20 right-10 from-red-300 via-rose-300
        to-pink-200 opacity-40 blur-3xl dark:opacity-0"></div>
    <div class="h-[35vh] w-[45vh] rounded-full bg-gradient-to-b dark:h-[28vh] absolute top-28 left-1/4 from-red-300
        via-rose-200 to-pink-100 opacity-60 blur-3xl dark:from-red-600 dark:via-rose-500 dark:to-pink-400
        dark:opacity-64"></div>
  </div>
  <header class="bg-white/80 dark:bg-neutral-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-neutral-800
      sticky top-0 z-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
      <div class="items-center justify-between h-16 flex">
        <div class="items-center flex gap-2">
          <div class="w-10 h-10 bg-gradient-to-br rounded-lg items-center justify-center from-red-500 to-red-600
              dark:from-red-600 dark:to-red-700 flex">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_2Qzq4Lyf9">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
          </div>
          <span class="text-xl font-bold text-gray-900 dark:text-white">MyShop</span>
        </div>
        <nav class="md:flex items-center hidden gap-6">
          <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-300 hover:text-red-600
              dark:hover:text-red-400 transition">Home</a>
          <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-300 hover:text-red-600
              dark:hover:text-red-400 transition">Products</a>
          <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-300 hover:text-red-600
              dark:hover:text-red-400 transition">About</a>
          <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-300 hover:text-red-600
              dark:hover:text-red-400 transition">Contact</a>
        </nav>
        <div class="items-center flex gap-3">
          <button type="submit" class="relative p-2 dark:text-neutral-300 hover:text-red-600 dark:hover:text-red-400
              transition text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_FR2qOwzQ0">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
            <span class="bg-red-600 text-white text-xs w-5 h-5 rounded-full items-center justify-center absolute -top-1
                -right-1 flex">3</span>
          </button>
          <button type="submit" class="p-2 dark:text-neutral-300 hover:text-red-600 dark:hover:text-red-400 transition
              text-gray-600 md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_x02p8wrvc">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>
  <main class="mx-auto px-4 sm:px-6 lg:px-8 lg:py-12 py-8 max-w-7xl">
    <div class="mb-8">
      <p class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2 dark:text-white">Shopping Cart</p>
      <p class="text-gray-600 dark:text-neutral-400">Review your items and proceed to checkout</p>
    </div>
    <div class="lg:grid-cols-3 lg:gap-8 grid grid-cols-1 gap-6">
      <div class="lg:col-span-2 space-y-4">
        <div class="bg-white dark:bg-neutral-900 rounded-xl sm:p-6 shadow-sm border border-gray-200
            dark:border-neutral-800 p-4 hover:shadow-md transition">
          <div class="sm:flex-row flex flex-col gap-4">
            <div class="w-full sm:w-32 h-32 bg-gray-100 dark:bg-neutral-800 rounded-lg overflow-hidden flex-shrink-0">
              <img alt="Product" src="https://placehold.co/200x200/f87171/ffffff?text=Product+1" class="object-cover
                  w-full h-full">
            </div>
            <div class="flex-1 space-y-3">
              <div class="items-start justify-between flex gap-4">
                <div>
                  <p class="text-lg font-semibold text-gray-900 dark:text-white">Premium Wireless Headphones</p>
                  <p class="text-sm text-gray-600 mt-1 dark:text-neutral-400">Color: Black | Size: Standard</p>
                </div>
                <button type="submit" class="hover:text-red-600 dark:hover:text-red-400 transition flex-shrink-0
                    text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_vz5VBQx5r">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                </button>
              </div>
              <div class="items-center justify-between flex">
                <div class="items-center flex gap-3">
                  <button type="submit" class="border border-gray-300 dark:border-neutral-700 flex dark:text-neutral-300
                      hover:bg-gray-100 dark:hover:bg-neutral-800 transition w-8 h-8 rounded-lg items-center
                      justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_dRX5y8aab">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                  </button>
                  <span class="text-gray-900 font-medium w-8 text-center dark:text-white">1</span>
                  <button type="submit" class="border border-gray-300 dark:border-neutral-700 flex dark:text-neutral-300
                      hover:bg-gray-100 dark:hover:bg-neutral-800 transition w-8 h-8 rounded-lg items-center
                      justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_Knumnrx2A">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                  </button>
                </div>
                <div class="text-right">
                  <p class="text-xl font-bold text-red-600 dark:text-red-500">$299.00</p>
                </div>
              </div>
              <div class="items-center pt-2 flex gap-4 border-t border-gray-100 dark:border-neutral-800">
                <button type="submit" class="dark:text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition
                    flex gap-1 text-sm text-gray-600 items-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_E16rNSRKq">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                  Save for later
                </button>
                <button type="submit" class="dark:text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition
                    flex gap-1 text-sm text-gray-600 items-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_nHTfDSXGj">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-neutral-900 rounded-xl sm:p-6 shadow-sm border border-gray-200
            dark:border-neutral-800 p-4 hover:shadow-md transition">
          <div class="sm:flex-row flex flex-col gap-4">
            <div class="w-full sm:w-32 h-32 bg-gray-100 dark:bg-neutral-800 rounded-lg overflow-hidden flex-shrink-0">
              <img alt="Product" src="https://placehold.co/200x200/fb923c/ffffff?text=Product+2" class="object-cover
                  w-full h-full">
            </div>
            <div class="flex-1 space-y-3">
              <div class="items-start justify-between flex gap-4">
                <div>
                  <p class="text-lg font-semibold text-gray-900 dark:text-white">Smart Watch Series 7</p>
                  <p class="text-sm text-gray-600 mt-1 dark:text-neutral-400">Color: Silver | Size: 42mm</p>
                </div>
                <button type="submit" class="hover:text-red-600 dark:hover:text-red-400 transition flex-shrink-0
                    text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_JU8ifd61T">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                </button>
              </div>
              <div class="items-center justify-between flex">
                <div class="items-center flex gap-3">
                  <button type="submit" class="border border-gray-300 dark:border-neutral-700 flex dark:text-neutral-300
                      hover:bg-gray-100 dark:hover:bg-neutral-800 transition w-8 h-8 rounded-lg items-center
                      justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_W9n0N8w1J">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                  </button>
                  <span class="text-gray-900 font-medium w-8 text-center dark:text-white">2</span>
                  <button type="submit" class="border border-gray-300 dark:border-neutral-700 flex dark:text-neutral-300
                      hover:bg-gray-100 dark:hover:bg-neutral-800 transition w-8 h-8 rounded-lg items-center
                      justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_PDFMLCg5J">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                  </button>
                </div>
                <div class="text-right">
                  <p class="text-xl font-bold text-red-600 dark:text-red-500">$798.00</p>
                  <p class="text-xs text-gray-500 dark:text-neutral-500">$399.00 each</p>
                </div>
              </div>
              <div class="items-center pt-2 flex gap-4 border-t border-gray-100 dark:border-neutral-800">
                <button type="submit" class="dark:text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition
                    flex gap-1 text-sm text-gray-600 items-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_nt4g9y0rb">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                  Save for later
                </button>
                <button type="submit" class="dark:text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition
                    flex gap-1 text-sm text-gray-600 items-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_0nw8Dg8Hg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-neutral-900 rounded-xl sm:p-6 shadow-sm border border-gray-200
            dark:border-neutral-800 p-4 hover:shadow-md transition">
          <div class="sm:flex-row flex flex-col gap-4">
            <div class="w-full sm:w-32 h-32 bg-gray-100 dark:bg-neutral-800 rounded-lg overflow-hidden flex-shrink-0">
              <img alt="Product" src="https://placehold.co/200x200/f472b6/ffffff?text=Product+3" class="object-cover
                  w-full h-full">
            </div>
            <div class="flex-1 space-y-3">
              <div class="items-start justify-between flex gap-4">
                <div>
                  <p class="text-lg font-semibold text-gray-900 dark:text-white">Laptop Backpack Pro</p>
                  <p class="text-sm text-gray-600 mt-1 dark:text-neutral-400">Color: Navy Blue | Material: Nylon</p>
                </div>
                <button type="submit" class="hover:text-red-600 dark:hover:text-red-400 transition flex-shrink-0
                    text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_01pRrGHbB">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                </button>
              </div>
              <div class="items-center justify-between flex">
                <div class="items-center flex gap-3">
                  <button type="submit" class="border border-gray-300 dark:border-neutral-700 flex dark:text-neutral-300
                      hover:bg-gray-100 dark:hover:bg-neutral-800 transition w-8 h-8 rounded-lg items-center
                      justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_Jny4PR2zX">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                  </button>
                  <span class="text-gray-900 font-medium w-8 text-center dark:text-white">1</span>
                  <button type="submit" class="border border-gray-300 dark:border-neutral-700 flex dark:text-neutral-300
                      hover:bg-gray-100 dark:hover:bg-neutral-800 transition w-8 h-8 rounded-lg items-center
                      justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_6o9yV63zR">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                  </button>
                </div>
                <div class="text-right">
                  <p class="text-xl font-bold text-red-600 dark:text-red-500">$89.00</p>
                </div>
              </div>
              <div class="items-center pt-2 flex gap-4 border-t border-gray-100 dark:border-neutral-800">
                <button type="submit" class="dark:text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition
                    flex gap-1 text-sm text-gray-600 items-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_I22bdAyQ1">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                  Save for later
                </button>
                <button type="submit" class="dark:text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition
                    flex gap-1 text-sm text-gray-600 items-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_CcdHoYon5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gradient-to-r rounded-xl sm:p-6 from-red-50 to-rose-50 dark:from-red-950/20 dark:to-rose-950/20
            border border-red-200 dark:border-red-900/30 p-4">
          <div class="items-start flex gap-3">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg items-center justify-center flex
                flex-shrink-0">
              <svg class="w-5 h-5 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_e22vQouh0">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-gray-900 mb-2 dark:text-white">Have a promo code?</p>
              <div class="flex gap-2">
                <input type="text" placeholder="Enter code here" class="flex-1 border border-gray-300
                    dark:border-neutral-700 dark:text-white placeholder-gray-500 dark:placeholder-neutral-500
                    focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-600 px-4 py-2 bg-white
                    dark:bg-neutral-900 rounded-lg text-gray-900">
                <button type="submit" class="hover:bg-red-700 dark:hover:bg-red-800 transition px-6 py-2 bg-red-600
                    dark:bg-red-700 text-white font-medium rounded-lg">Apply</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-800 p-6
            sticky top-24">
          <p class="text-xl font-bold text-gray-900 mb-6 dark:text-white">Order Summary</p>
          <div class="mb-6 space-y-4">
            <div class="justify-between text-gray-600 flex dark:text-neutral-400">
              <span>Subtotal (3 items)</span>
              <span class="font-medium text-gray-900 dark:text-white">$1,186.00</span>
            </div>
            <div class="justify-between text-gray-600 flex dark:text-neutral-400">
              <span>Shipping</span>
              <span class="font-medium text-gray-900 dark:text-white">$15.00</span>
            </div>
            <div class="justify-between text-gray-600 flex dark:text-neutral-400">
              <span>Tax (10%)</span>
              <span class="font-medium text-gray-900 dark:text-white">$118.60</span>
            </div>
            <div class="justify-between items-center text-green-600 flex dark:text-green-500">
              <span class="items-center flex gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_qOldbRJ8o">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                Discount
              </span>
              <span class="font-medium">-$50.00</span>
            </div>
          </div>
          <div class="pt-4 mb-6 border-t border-gray-200 dark:border-neutral-800">
            <div class="justify-between items-center flex">
              <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
              <span class="text-2xl font-bold text-red-600 dark:text-red-500">$1,269.60</span>
            </div>
            <p class="text-xs text-gray-500 mt-1 dark:text-neutral-500">Including all taxes and fees</p>
          </div>
          <button type="submit" class="hover:bg-red-700 dark:hover:bg-red-800 transition dark:shadow-red-900/30 w-full
              py-3 bg-red-600 dark:bg-red-700 text-white font-semibold rounded-lg shadow-lg shadow-red-500/20
              mb-3">Proceed to Checkout</button>
          <button type="submit" class="hover:bg-gray-200 dark:hover:bg-neutral-700 dark:text-white transition w-full
              py-3 bg-gray-100 dark:bg-neutral-800 text-gray-900 font-medium rounded-lg">Continue Shopping</button>
          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-800 space-y-3">
            <div class="items-start flex gap-3">
              <svg class="w-5 h-5 text-red-600 mt-0.5 dark:text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_0JhcVF4nD">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Secure Payment</p>
                <p class="text-xs text-gray-500 dark:text-neutral-500">Your payment is protected</p>
              </div>
            </div>
            <div class="items-start flex gap-3">
              <svg class="w-5 h-5 text-red-600 mt-0.5 dark:text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_Hj60MvkfC">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Free Shipping</p>
                <p class="text-xs text-gray-500 dark:text-neutral-500">On orders over $100</p>
              </div>
            </div>
            <div class="items-start flex gap-3">
              <svg class="w-5 h-5 text-red-600 mt-0.5 dark:text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_O0a7WhamN">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Easy Returns</p>
                <p class="text-xs text-gray-500 dark:text-neutral-500">30-day return policy</p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-sm mt-6 border border-gray-200
            dark:border-neutral-800 p-6">
          <p class="text-sm font-semibold text-gray-900 mb-4 dark:text-white">We Accept</p>
          <div class="grid grid-cols-4 gap-3">
            <div class="aspect-video bg-gray-100 dark:bg-neutral-800 rounded-lg items-center justify-center flex border
                border-gray-200 dark:border-neutral-700">
              <svg class="w-8 h-8 text-gray-400 dark:text-neutral-600" fill="currentColor" viewBox="0 0 24 24" id="Windframe_iu8FuT6px">
                                    <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 2v3h16V6H4zm0 5v7h16v-7H4z"></path>
                                </svg>
            </div>
            <div class="aspect-video bg-gray-100 dark:bg-neutral-800 rounded-lg items-center justify-center flex border
                border-gray-200 dark:border-neutral-700">
              <svg class="w-8 h-8 text-gray-400 dark:text-neutral-600" fill="currentColor" viewBox="0 0 24 24" id="Windframe_H1HiTRhqF">
                                    <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 2v3h16V6H4zm0 5v7h16v-7H4z"></path>
                                </svg>
            </div>
            <div class="aspect-video bg-gray-100 dark:bg-neutral-800 rounded-lg items-center justify-center flex border
                border-gray-200 dark:border-neutral-700">
              <svg class="w-8 h-8 text-gray-400 dark:text-neutral-600" fill="currentColor" viewBox="0 0 24 24" id="Windframe_jgjxK66sq">
                                    <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 2v3h16V6H4zm0 5v7h16v-7H4z"></path>
                                </svg>
            </div>
            <div class="aspect-video bg-gray-100 dark:bg-neutral-800 rounded-lg items-center justify-center flex border
                border-gray-200 dark:border-neutral-700">
              <svg class="w-8 h-8 text-gray-400 dark:text-neutral-600" fill="currentColor" viewBox="0 0 24 24" id="Windframe_mI0hdNwIT">
                                    <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 2v3h16V6H4zm0 5v7h16v-7H4z"></path>
                                </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-12">
      <div class="items-center justify-between mb-6 flex">
        <p class="text-2xl font-bold text-gray-900 dark:text-white">You May Also Like</p>
        <button type="submit" class="dark:text-red-500 hover:text-red-700 dark:hover:text-red-400 flex gap-2 transition
            text-red-600 font-medium items-center">
          View All
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_BFxAf7Yfv">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
        </button>
      </div>
      <div class="sm:grid-cols-2 lg:grid-cols-4 grid grid-cols-1 gap-6">
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-800
            overflow-hidden hover:shadow-lg transition group">
          <div class="aspect-square bg-gray-100 dark:bg-neutral-800 overflow-hidden relative">
            <img alt="Product" src="https://placehold.co/400x400/fca5a5/ffffff?text=Suggested+1" class="object-cover
                group-hover:scale-105 transition duration-300 w-full h-full">
            <button type="submit" class="absolute top-3 right-3 backdrop-blur-sm flex dark:text-neutral-400
                hover:text-red-600 dark:hover:text-red-500 transition w-9 h-9 bg-white/90 dark:bg-neutral-900/90
                rounded-full items-center justify-center text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_1e2zrRzBZ">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
            </button>
          </div>
          <div class="p-4">
            <p class="font-semibold text-gray-900 mb-1 dark:text-white">Wireless Mouse</p>
            <p class="text-sm text-gray-600 mb-3 dark:text-neutral-400">Ergonomic Design</p>
            <div class="items-center justify-between flex">
              <span class="text-lg font-bold text-red-600 dark:text-red-500">$29.99</span>
              <button type="submit" class="hover:bg-red-700 dark:hover:bg-red-800 transition px-4 py-2 bg-red-600
                  dark:bg-red-700 text-white text-sm font-medium rounded-lg">Add</button>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-800
            overflow-hidden hover:shadow-lg transition group">
          <div class="aspect-square bg-gray-100 dark:bg-neutral-800 overflow-hidden relative">
            <img alt="Product" src="https://placehold.co/400x400/fdba74/ffffff?text=Suggested+2" class="object-cover
                group-hover:scale-105 transition duration-300 w-full h-full">
            <button type="submit" class="absolute top-3 right-3 backdrop-blur-sm flex dark:text-neutral-400
                hover:text-red-600 dark:hover:text-red-500 transition w-9 h-9 bg-white/90 dark:bg-neutral-900/90
                rounded-full items-center justify-center text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_U0vulAG00">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
            </button>
          </div>
          <div class="p-4">
            <p class="font-semibold text-gray-900 mb-1 dark:text-white">USB-C Hub</p>
            <p class="text-sm text-gray-600 mb-3 dark:text-neutral-400">7-in-1 Adapter</p>
            <div class="items-center justify-between flex">
              <span class="text-lg font-bold text-red-600 dark:text-red-500">$49.99</span>
              <button type="submit" class="hover:bg-red-700 dark:hover:bg-red-800 transition px-4 py-2 bg-red-600
                  dark:bg-red-700 text-white text-sm font-medium rounded-lg">Add</button>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-800
            overflow-hidden hover:shadow-lg transition group">
          <div class="aspect-square bg-gray-100 dark:bg-neutral-800 overflow-hidden relative">
            <img alt="Product" src="https://placehold.co/400x400/fda4af/ffffff?text=Suggested+3" class="object-cover
                group-hover:scale-105 transition duration-300 w-full h-full">
            <button type="submit" class="absolute top-3 right-3 backdrop-blur-sm flex dark:text-neutral-400
                hover:text-red-600 dark:hover:text-red-500 transition w-9 h-9 bg-white/90 dark:bg-neutral-900/90
                rounded-full items-center justify-center text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_kpGi2SqWX">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
            </button>
          </div>
          <div class="p-4">
            <p class="font-semibold text-gray-900 mb-1 dark:text-white">Phone Stand</p>
            <p class="text-sm text-gray-600 mb-3 dark:text-neutral-400">Adjustable Angle</p>
            <div class="items-center justify-between flex">
              <span class="text-lg font-bold text-red-600 dark:text-red-500">$19.99</span>
              <button type="submit" class="hover:bg-red-700 dark:hover:bg-red-800 transition px-4 py-2 bg-red-600
                  dark:bg-red-700 text-white text-sm font-medium rounded-lg">Add</button>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-gray-200 dark:border-neutral-800
            overflow-hidden hover:shadow-lg transition group">
          <div class="aspect-square bg-gray-100 dark:bg-neutral-800 overflow-hidden relative">
            <img alt="Product" src="https://placehold.co/400x400/fb7185/ffffff?text=Suggested+4" class="object-cover
                group-hover:scale-105 transition duration-300 w-full h-full">
            <button type="submit" class="absolute top-3 right-3 backdrop-blur-sm flex dark:text-neutral-400
                hover:text-red-600 dark:hover:text-red-500 transition w-9 h-9 bg-white/90 dark:bg-neutral-900/90
                rounded-full items-center justify-center text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_zRHSy1QIY">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
            </button>
          </div>
          <div class="p-4">
            <p class="font-semibold text-gray-900 mb-1 dark:text-white">Cable Organizer</p>
            <p class="text-sm text-gray-600 mb-3 dark:text-neutral-400">Set of 10 Pieces</p>
            <div class="items-center justify-between flex">
              <span class="text-lg font-bold text-red-600 dark:text-red-500">$12.99</span>
              <button type="submit" class="hover:bg-red-700 dark:hover:bg-red-800 transition px-4 py-2 bg-red-600
                  dark:bg-red-700 text-white text-sm font-medium rounded-lg">Add</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer class="bg-gray-50 dark:bg-neutral-900 mt-16 border-t border-gray-200 dark:border-neutral-800">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-12 max-w-7xl">
      <div class="md:grid-cols-4 mb-8 grid grid-cols-1 gap-8">
        <div>
          <div class="items-center mb-4 flex gap-2">
            <div class="w-10 h-10 bg-gradient-to-br rounded-lg items-center justify-center from-red-500 to-red-600
                dark:from-red-600 dark:to-red-700 flex">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_QLNMooEjl">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
            </div>
            <span class="text-xl font-bold text-gray-900 dark:text-white">MyShop</span>
          </div>
          <p class="text-sm text-gray-600 mb-4 dark:text-neutral-400">Your trusted online shopping destination for
              quality products at great prices.</p>
          <div class="flex gap-3">
            <a href="/QtMrvKzylHlNN7mNRG4O#" class="w-9 h-9 bg-gray-200 dark:bg-neutral-800 rounded-lg items-center
                justify-center text-gray-600 flex dark:text-neutral-400 hover:bg-red-600 hover:text-white
                dark:hover:bg-red-700 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" id="Windframe_hO2ENI4EX">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                                </svg>
            </a>
            <a href="/QtMrvKzylHlNN7mNRG4O#" class="w-9 h-9 bg-gray-200 dark:bg-neutral-800 rounded-lg items-center
                justify-center text-gray-600 flex dark:text-neutral-400 hover:bg-red-600 hover:text-white
                dark:hover:bg-red-700 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" id="Windframe_dOURujb1t">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path>
                                </svg>
            </a>
            <a href="/QtMrvKzylHlNN7mNRG4O#" class="w-9 h-9 bg-gray-200 dark:bg-neutral-800 rounded-lg items-center
                justify-center text-gray-600 flex dark:text-neutral-400 hover:bg-red-600 hover:text-white
                dark:hover:bg-red-700 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" id="Windframe_qR6gmgH8B">
                                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"></path>
                                </svg>
            </a>
          </div>
        </div>
        <div>
          <p class="font-semibold text-gray-900 mb-4 dark:text-white">Shop</p>
          <ul class="text-sm space-y-2">
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">All Products</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">New Arrivals</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Best Sellers</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Sale</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Gift Cards</a>
            </li>
          </ul>
        </div>
        <div>
          <p class="font-semibold text-gray-900 mb-4 dark:text-white">Support</p>
          <ul class="text-sm space-y-2">
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Help Center</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Track Order</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Shipping Info</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Returns</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Contact Us</a>
            </li>
          </ul>
        </div>
        <div>
          <p class="font-semibold text-gray-900 mb-4 dark:text-white">Company</p>
          <ul class="text-sm space-y-2">
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">About Us</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Careers</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Privacy Policy</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Terms of Service</a>
            </li>
            <li>
              <a href="/QtMrvKzylHlNN7mNRG4O#" class="text-gray-600 dark:text-neutral-400 hover:text-red-600
                  dark:hover:text-red-400 transition">Affiliates</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="pt-8 border-t border-gray-200 dark:border-neutral-800">
        <p class="text-sm text-gray-600 text-center dark:text-neutral-400">© 2024 MyShop. All rights reserved.</p>
      </div>
    </div>
  </footer>
</div>
</body>

<!-- swiper js masih pake cdn  -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/assets/js/users/dashboard.js"></script>
</body>
</html> 