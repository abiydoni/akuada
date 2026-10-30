<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. Marketplace Public Web (Tokopedia B2B style)
$routes->get('/', 'Home::index');
$routes->get('products', 'ProductController::index');
$routes->get('product/(:segment)', 'ProductController::detail/$1');
$routes->post('product/sample/(:num)', 'ProductController::requestSample/$1');
$routes->get('page/(:segment)', 'PageController::show/$1');

// 2. B2B Cart & Checkout
$routes->get('cart', 'CartController::index');
$routes->post('cart/add', 'CartController::add');
$routes->post('cart/update', 'CartController::update');
$routes->get('cart/remove/(:num)', 'CartController::remove/$1');
$routes->get('checkout', 'CartController::checkout');
$routes->post('checkout/process', 'CartController::processCheckout');

// 3. Request For Quotation (RFQ)
$routes->get('rfq', 'RfqController::index');
$routes->get('rfq/create', 'RfqController::create');
$routes->post('rfq/store', 'RfqController::store');
$routes->get('rfq/(:num)', 'RfqController::detail/$1');

// 4. Authentication & Quick Role Switcher
$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login/process', 'AuthController::processLogin');
    $routes->get('register', 'AuthController::register');
    $routes->post('register/process', 'AuthController::processRegister');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('switch-role/(:segment)', 'AuthController::switchRole/$1');
});

// 5. Dashboard Admin (Platform Superadmin)
$routes->group('admin', function ($routes) {
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // 1 & 2. Companies (Vendors & Buyers)
    $routes->get('vendors', 'AdminController::vendors');
    $routes->get('vendors/create', 'AdminController::createVendor');
    $routes->post('vendors/store', 'AdminController::storeVendor');
    $routes->post('vendors/update-fee', 'AdminController::updateVendorFee');
    $routes->get('vendors/toggle/(:num)', 'AdminController::toggleVendorVerification/$1');
    $routes->get('buyers', 'AdminController::buyers');
    $routes->get('buyers/create', 'AdminController::createBuyer');
    $routes->post('buyers/store', 'AdminController::storeBuyer');
    $routes->post('buyers/limit', 'AdminController::updateBuyerLimit');

    // 3. Categories Management
    $routes->get('categories', 'AdminController::categories');
    $routes->post('categories/store', 'AdminController::storeCategory');
    $routes->post('categories/update/(:num)', 'AdminController::updateCategory/$1');
    $routes->get('categories/delete/(:num)', 'AdminController::deleteCategory/$1');

    // 4 & 5. Products & Product Price Tiers
    $routes->get('products', 'AdminController::products');
    $routes->get('products/toggle/(:num)', 'AdminController::toggleProductStatus/$1');
    $routes->get('products/delete/(:num)', 'AdminController::deleteProduct/$1');

    // 6, 7 & 8. Orders, Order Items & Order Status Logs
    $routes->get('transactions', 'AdminController::transactions');
    $routes->get('orders/(:num)', 'AdminController::orderDetail/$1');
    $routes->post('transactions/update', 'AdminController::updateOrderStatus');
    $routes->get('orders/invoice-customer/(:num)', 'AdminController::invoiceCustomer/$1');
    $routes->get('orders/invoice-vendor/(:num)', 'AdminController::invoiceVendor/$1');
    $routes->get('status-logs', 'AdminController::statusLogs');
    $routes->get('debts', 'AdminController::debts');
    $routes->post('debts/confirm-buyer', 'AdminController::confirmBuyerPayment');
    $routes->post('debts/pay-vendor', 'AdminController::payVendorDebt');

    // 9 & 10. RFQs & RFQ Items
    $routes->get('rfqs', 'AdminController::rfqs');
    $routes->get('rfqs/(:num)', 'AdminController::rfqDetail/$1');
    $routes->post('rfqs/update-status', 'AdminController::updateRfqStatus');

    // 11. Users Management (Admin, Vendor, Customer Logins)
    $routes->get('users', 'AdminController::users');
    $routes->post('users/store', 'AdminController::storeUser');
    $routes->post('users/update/(:num)', 'AdminController::updateUser/$1');
    $routes->get('users/toggle/(:num)', 'AdminController::toggleUserStatus/$1');
    $routes->post('users/reset-password/(:num)', 'AdminController::resetUserPassword/$1');

    // 12. System Settings
    $routes->get('settings', 'AdminController::settings');
    $routes->post('settings/update', 'AdminController::updateSettings');
});

// 6. Dashboard Vendor (Mitra Supplier B2B)
$routes->group('vendor', function ($routes) {
    $routes->get('/', 'VendorController::dashboard');
    $routes->get('dashboard', 'VendorController::dashboard');
    $routes->get('orders', 'VendorController::orders');
    $routes->get('orders/(:num)', 'VendorController::orderDetail/$1');
    $routes->get('orders/vendor-invoice/(:num)', 'VendorController::vendorInvoice/$1');
    $routes->get('orders/invoice/(:num)', 'VendorController::vendorInvoice/$1');
    $routes->post('orders/update-shipping', 'VendorController::updateShipping');
    $routes->get('products', 'VendorController::products');
    $routes->get('products/new', 'VendorController::newProduct');
    $routes->post('products/store', 'VendorController::storeProduct');
    $routes->get('products/edit/(:num)', 'VendorController::editProduct/$1');
    $routes->post('products/update/(:num)', 'VendorController::updateProduct/$1');
    $routes->get('products/delete/(:num)', 'VendorController::deleteProduct/$1');
    $routes->get('rfqs', 'VendorController::rfqs');
    $routes->post('rfqs/quote', 'VendorController::submitQuote');
    $routes->get('company-profile', 'VendorController::companyProfile');
    $routes->get('settlements', 'VendorController::settlements');
    $routes->post('settlements/update-bank', 'VendorController::updateBankAccount');
});

// 7. Dashboard Customer (Corporate Buyer / Perusahaan)
$routes->group('customer', function ($routes) {
    $routes->get('/', 'CustomerController::dashboard');
    $routes->get('dashboard', 'CustomerController::dashboard');
    $routes->get('orders', 'CustomerController::orders');
    $routes->get('orders/(:num)', 'CustomerController::orderDetail/$1');
    $routes->get('orders/invoice/(:num)', 'CustomerController::invoice/$1');
    $routes->post('orders/sign-bast', 'CustomerController::signBast');
    $routes->get('rfqs', 'CustomerController::rfqs');
    $routes->get('rfqs/accept/(:num)', 'CustomerController::acceptQuote/$1');
    $routes->get('tax-invoices', 'CustomerController::taxInvoices');
    $routes->get('credit-limit', 'CustomerController::creditLimit');
    $routes->post('credit-limit/request', 'CustomerController::requestLimitIncrease');
    $routes->get('company-profile', 'CustomerController::companyProfile');
    $routes->get('payables', 'CustomerController::payables');
    $routes->post('payables/pay', 'CustomerController::payDebt');
});
