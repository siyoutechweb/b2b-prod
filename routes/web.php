<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/key', function () {
    return \Illuminate\Support\Str::random(32);
});
$router->get('/testsupplier', ['uses' => 'Order\SupplierOrdersController@createSupplier']);
//route for testlogin
$router->post('/testlogin', ['uses' => 'AuthController@testlogin']);
    
$router->post('/login', [
    'uses' => 'AuthController@login'
]);

//$router->post('/sign_up', ['uses' => 'User\UsersController@signUp']);
$router->post('/sign_up/shop', ['uses' => 'User\UsersController@signUpShop']);
$router->get('/test/{order_id}', ['uses' => 'Order\ShopOrdersController@ifSalesmanager']);
$router->post('/sign_up', ['uses' => 'User\UsersController@createUsers']);
$router->get('/roles', ['uses' => 'User\UsersController@getRoles']);
// $router->group(['prefix' => 'infos', "roles" => ["SalesManager"]], function () use ($router) {
//     $router->get('/', ["uses" => "AuthController@infos", "as" => "infos"]);
// });

$router->get('/infos', [
    "uses" => "AuthController@infos",
    "as" => "infos",
    "roles" => ["salesmanager"]
]);

$router->post('/addSalesManager', ['uses' => 'User\UsersController@addSalesManager']);
$router->post('/addSupplier', ['uses' => 'User\UsersController@addSupplier']);
$router->post('/addShop_Owner', ['uses' => 'User\UsersController@addShopOwnerByAdmin']);
$router->post('/addShopowner', ['uses' => 'User\UsersController@addShop_Owner']);


/**
 * Routes for resource superadmin
 */

$router->get('/getInvalidUsersLast', ['uses' => 'User\UsersController@getInvalidUsersLast']);
$router->get('/getInvalidUsers', ['uses' => 'User\UsersController@getInvalidUsers']);
$router->get('/getvalidUsers', ['uses' => 'User\UsersController@getvalidUsers']);
$router->put('/validateUser/{userId}', ['uses' => 'User\UsersController@validateUser']);
$router->delete('/{id}', ['uses' => 'User\UsersController@deleteUser']);
$router->get('/all', ['uses' => 'User\UsersController@UsersList']);
$router->put('/{id}', ['uses' => 'User\UsersController@UpdateUser']);
$router->put('/block/{user_id}', ['uses' => 'User\UsersController@blockUser']);

/**
 * Routes for resource users
 */
$router->group(['prefix' => 'users'], function () use ($router) {
    $router->get('/search', ['uses' => 'User\UsersController@searchSupplier']);
    $router->get('/suppliers', ['uses' => 'User\UsersController@getSupplierList']);
    $router->get('/shops', ['uses' => 'User\UsersController@getShopsList']);
    $router->get('/SM', ['uses' => 'User\UsersController@getSalesManagersList']);
    $router->get('/GetUsersByRole/{id}', ['uses' => 'User\UsersController@GetUsersByRole']);
    $router->get('/GetUserByRole/{id}', ['uses' => 'User\UsersController@GetUserByRole']);
    $router->get('/Account', ['uses' => 'User\UsersController@account']);
    $router->put('/{id}', ['uses' => 'User\UsersController@UpdateUser']);
    $router->get('/', ['uses' => 'User\UsersController@UsersList']);
    $router->get('/{id}', ['uses' => 'User\UsersController@ShowUser']);
    $router->delete('/{id}', ['uses' => 'User\UsersController@deleteUser']);
    
    // $router->post('/addSalesManager', ['uses' => 'User\UsersController@affectSalesManagerToShop']);

});
/**
 * Routes for resource supplier
 */
$router->group(['prefix' => 'supplier', ['middleware' => 'role:Supplier']], function () use ($router) {
    $router->get('/orders/shops', ['uses' => 'User\UsersController@getSupplierOrderShop']);
    $router->get('/salesmanagers/shops', ['uses' => 'User\UsersController@getSupplierSalesmanagerShop']);
    $router->post('/shops', ['uses' => 'User\UsersController@linkSalesManagerToShop']);
    $router->post('/shop', ['uses' => 'User\UsersController@linkShopTosupplier']);
    $router->post('/salesmanager', ['uses' => 'User\UsersController@addSalesManagerToSupplier']);
    $router->get('/salesmanager', ['uses' => 'User\UsersController@getSalesManagerList']);
    $router->put('/salesmanager', ['uses' => 'User\UsersController@updateSalesmanagerCommission']);
    $router->put('/shops', ['uses' => 'User\UsersController@updatedepositshop']);
    $router->post('/salesmanager/search', ['uses' => 'User\UsersController@getSalesManagerByEmail']);
    $router->post('/shop/search', ['uses' => 'User\UsersController@getShopOwnerByEmail']);
});

$router->group(['prefix' => 'logistic', ['middleware' => 'role:Supplier']], function () use ($router) {

    $router->post('/', ['uses' => 'Logistics\LogisticServicesController@getLogisticService']);
    $router->post('/cancel', ['uses' => 'Logistics\LogisticServicesController@removeLogisticService']);
});   

$router->group(['prefix' => 'products'], function () use ($router) {
    $router->group(['middleware' => 'role:Supplier'], function () use ($router) {
        $router->post('/', ['uses' => 'Product\ProductsController@addProduct']);
        $router->post('/upload', ['uses' => 'Product\ProductsController@addProductsWithExcel']);
        $router->post('/addNewProd', ['uses' => 'Product\ProductBasesController@addProductBase']);
        $router->put('/updateProd', ['uses' => 'Product\ProductBasesController@updateProductWithItem']);
        $router->get('/List', ['uses' => 'Product\ProductBasesController@getProductListBySupplier']);
        
        $router->post('/addNewItem', ['uses' => 'Product\ProductItemsController@addItem']);
        $router->post('/addItemCriteria', ['uses' => 'Product\ProductItemsController@addItemCriteria']);
        $router->put('/updateItemCriteria', ['uses' => 'Product\ProductItemsController@updateItemCriteria']);
        $router->get('/generate/item/barcode', ['uses' => 'Product\ProductItemsController@generateBarcode']);
        $router->delete('/deleteItemCriteria', ['uses' => 'Product\ProductItemsController@deleteItemCriteria']);

        $router->get('/categories', ['uses' => 'Product\ProductBasesController@getSupplierCategories']);
        // $router->post('/uploadImage', ['uses' => 'Product\ProductItemsController@uploadImage']);
        $router->get('items', ['uses' => 'Product\ProductItemsController@getSupplierItems']);
        $router->get('/items/discount', ['uses' => 'Product\DiscountsController@getSupplierDiscountItems']);
        $router->put('/item/{item_id}', ['uses' => 'Product\ProductItemsController@updateItem']);
        $router->delete('/deleteProd/{product_id}', ['uses' => 'Product\ProductBasesController@deleteProductBase']);
        $router->delete('/deleteItem/{item_id}', ['uses' => 'Product\ProductItemsController@deleteItem']);
        $router->put('/base/{id}', ['uses' => 'Product\ProductBasesController@updateProductBase']);
    });
    $router->group(['middleware' => 'role:Supplier,SalesManager,Shop_Owner'], function () use ($router) {
       
        $router->get('/ItemList', ['uses' => 'Product\ProductItemsController@getProductItemList']);
        $router->get('/', ['uses' => 'Product\ProductBasesController@getProduct']);
        $router->get('/productList', ['uses' => 'Product\ProductBasesController@getProductList']);
        $router->get('/newArrival', ['uses' => 'Product\ProductBasesController@getLastAdded']);
        $router->get('/search/item', ['uses' => 'Product\ShowProductsController@searchItemByBarcode']);
        $router->get('/Item/{id}', ['uses' => 'Product\ProductItemsController@getItem']);
    });

    $router->group(['middleware' => 'role:SalesManager'], function () use ($router) {
        $router->get('/salesmanager', ['uses' => 'Product\ProductBasesController@getSalesmanagerProducts']);
    });

    $router->group(['middleware' => 'role:Shop_Owner'], function () use ($router) {
        $router->get('/suppliers/list', ['uses' => 'Product\ShowProductsController@getSuppliersList']);
        // $router->get('/filtred/list', ['uses' => 'Product\ShowProductsController@getfiltredProductList']);
        $router->get('/shop/list', ['uses' => 'Product\ShowProductsController@getShopProducts']);
        $router->get('/shop/all', ['uses' => 'Product\ShowProductsController@getShopProductList']);
        $router->get('/wish/list', ['uses' => 'Product\WishListsController@getWishList']);
        $router->post('/favorit', ['uses' => 'Product\WishListsController@wishList']);
        $router->get('/home/page', ['uses' => 'Product\ShowProductsController@HomePage']);
        $router->get('/best/seller/{supplier_id}', ['uses' => 'Product\ShowProductsController@supplierBestSeller']);
        // $router->get('/filtred/list', ['uses' => 'Product\ShowProductsController@getfiltredProductList']);
        $router->get('/purchased', ['uses' => 'Product\ShowProductsController@purchasedListBySupplier']);
        $router->get('/purchased/list', ['uses' => 'Product\ShowProductsController@purchasedList']);
        $router->get('/overview/{id}', ['uses' => 'Product\ShowProductsController@supplierOverview']);
    });

    $router->group(['middleware' => 'role:SalesManager,Supplier,Shop_Owner'], function () use ($router) {
        $router->get('/Search', ['uses' => 'Product\ProductItemsController@searchItem']);
    });


    $router->get('/filter', ['uses' => 'Product\ProductsController@getFilteredProductsList']);
    $router->get('/supplier', ['uses' => 'Product\ProductsController@getProductsBySupplier']);
    // $router->get('/salesmanager', ['uses' => 'Product\ProductsController@getSalesmanagerProducts']);
    // $router->get('/', ['uses' => 'Product\ProductsController@getProductBySupplier']);
    $router->get('/by_suppliers', ['uses' => 'Product\ProductsController@GetAllSupplierWithProducts']);
    $router->get('/by_category', ['uses' => 'Product\ProductsController@GetAllCategoryWithProducts']);
    $router->get('/{id}', ['uses' => 'Product\ProductsController@showProduct']);
    //$router->get('/', ['uses' => 'Product\ProductsController@productList']);
    $router->put('/{id}', ['uses' => 'Product\ProductsController@updateProduct']);
    $router->delete('/{id}', ['uses' => 'Product\ProductsController@deleteProduct']);
    $router->get('/category/{id}', ['uses' => 'Product\ProductsController@getProductByCategory']);
    // $router->get('/supplier/{id}', ['uses' => 'Product\ProductsController@getProductBySupplier']);
});
/**
 * Routes for resource categories
 */

// $router->group(['prefix' => 'categories'], function () use ($router) {
//     $router->get('/{id}', ['uses' => 'Category\CategoriesController@getCategoryList']);
// });

$router->group(['prefix' => 'categories', 'middleware' => 'role:Super_Admin,Supplier,Shop_Owner,SalesManager'], function () use ($router) {
    $router->post('/', ['uses' => 'Category\CategoriesController@addCategory']);
    $router->post('/criteria', ['uses' => 'Category\CategoriesController@addCriteria']);
    $router->get('/supplier', ['uses' => 'Category\CategoriesController@getSupplierCategories']);
    $router->get('/', ['uses' => 'Category\CategoriesController@getCategories']);
    $router->get('/list', ['uses' => 'Category\CategoriesController@getMobileCategories']);
    $router->post('/', ['uses' => 'Category\CategoriesController@addCategory']);
    $router->get('/getmostusedcategories', ['uses' => 'Category\CategoriesController@getmostusedcategories']);
    $router->delete('/{id}', ['uses' => 'Category\CategoriesController@deleteCategory']);
    $router->get('/{id}', ['uses' => 'Category\CategoriesController@ShowCategory']);
    $router->put('/{id}', ['uses' => 'Category\CategoriesController@updateCategory']);
    $router->get('/getCategoryParent/{id}', ['uses' => 'Category\CategoriesController@getCategoryParent']);
    $router->get('/getCategoryChild/{id}', ['uses' => 'Category\CategoriesController@getCategoryChild']);
    $router->get('/{id}', ['uses' => 'Category\CategoriesController@showCategory']);
    $router->delete('{categ_id}/{crit_id}', ['uses' => 'Category\CategoriesController@deleteCriteria']);

    $router->get('/supplier/{supplier_id}', ['uses' => 'Category\CategoriesController@getSupplierCategory']);
});


$router->group(['prefix' => 'orders'], function () use ($router) {
    
    $router->group(['middleware' => 'role:Shop_Owner'], function () use ($router) {
        $router->post('/', ['uses' => 'Order\ShopOrdersController@addOrder']);
        $router->get('/shop', ['uses' => 'Order\ShopOrdersController@getShopOwnerOrderList']);
        $router->get('/shop/invalid', ['uses' => 'Order\ShopOrdersController@getInvalidOrder']);
        $router->get('/shop/valid', ['uses' => 'Order\ShopOrdersController@getValidOrder']);
        $router->get('/shop/paid', ['uses' => 'Order\ShopOrdersController@getPaidOrder']);
    });
    $router->group(['prefix' => '/salesmanager',['middleware' => 'role:SalesManager']], function () use ($router) {
        $router->get('/', ['uses' => 'Order\SMOrdersController@getOrderList']);
        // $router->get('/valid/list', ['uses' => 'Order\SMOrdersController@getValidOrder']);
        // $router->get('/paid/list', ['uses' => 'Order\SMOrdersController@getPaidOrders']);
    });

    
    $router->group(['middleware' => 'role:Supplier'], function () use ($router) {
        $router->get('/invalid', ['uses' => 'Order\SupplierOrdersController@getSupplierInvalid']);
        $router->get('/valid', ['uses' => 'Order\SupplierOrdersController@getSupplierValidOrder']);
        $router->get('/paid', ['uses' => 'Order\SupplierOrdersController@getSupplierPaidOrder']);
        $router->get('/supplier', ['uses' => 'Order\SupplierOrdersController@getSupplierOrderList']);
        $router->put('/{order_id}', ['uses' => 'Order\SupplierOrdersController@updateOrderStatus']);
    });
    $router->get('/{orderId}', ['uses' => 'Order\OrdersController@getOrderById']);   
    
    
    // $router->post('/test', ['uses' => 'Order\OrdersController@ifSalesmanager']);
   
    // $router->get('/calculateCom', ['uses' => 'Order\OrdersController@calculateCommissionValue']);
    
    
    // $router->get('/getordersbydate', ['uses' => 'Order\OrdersController@GetOrderByDate']);
    // $router->get('/GetOrderBypaymentDate', ['uses' => 'Order\OrdersController@GetOrderBypaymentDate']);
    // $router->get('/GetOrderByprice', ['uses' => 'Order\OrdersController@GetOrderByOrderPrice']);
    // $router->get('/GetOrderByCommission', ['uses' => 'Order\OrdersController@GetOrderByCommission']);
    // $router->get('/GetOrderByCompany', ['uses' => 'Order\OrdersController@GetOrderByCompany']);
    // $router->get('/GetOrderByWeigh', ['uses' => 'Order\OrdersController@GetOrderByWeigh']);
    // $router->get('/GetOrderByPaymentMethod', ['uses' => 'Order\OrdersController@GetOrderByPaymentMethod']);
    // $router->get('/GetOrderBydaterange', ['uses' => 'Order\OrdersController@GetOrderBydaterange']);
    // $router->get('/GetOrderByStatus', ['uses' => 'Order\OrdersController@GetOrderByStatus']);
    // $router->get('/getbestsellingproducts', ['uses' => 'Order\OrdersController@getbestsellingproducts']);
   
});


/**
 * Routes for resource statut
 */
$router->group(['prefix' => 'statuts'], function () use ($router) {
    $router->post('/', ['uses' => 'Statut\StatutsController@addStatut']);
    $router->get('/', ['uses' => 'Statut\StatutsController@statutlist']);
    $router->get('/{id}', ['uses' => 'Statut\StatutsController@ShowStatut']);
    $router->delete('/{id}', ['uses' => 'Statut\StatutsController@DeleteStatut']);
    $router->put('/{id}', ['uses' => 'Statut\StatutsController@UpdateStatut']);
});


 $router->post('/update/user/{id}', ['uses' => 'User\UsersController@updateShopOwner']);

$router->group(['prefix' => 'tarifs'], function () use ($router) {
    $router->get('/', ['uses' => 'Logistics\Tarifs\TarifsController@getTrafis']);
});

$router->group(['prefix' => 'companies', ['middleware' => 'role:SalesManager,Supplier,Shop_Owner']], function () use ($router) {
    $router->post('/', ['uses' => 'Logistics\CompaniesController@AddCompany']);
    $router->put('/UpdateCompany/{id}', ['uses' => 'Logistics\CompaniesController@UpdateCompany']);
    $router->delete('/DeleteCompany/{id}', ['uses' => 'Logistics\CompaniesController@DeleteCompany']);
    $router->get('/GetCompany/{id}', ['uses' => 'Logistics\CompaniesController@GetCompany']);
    $router->get('/', ['uses' => 'Logistics\CompaniesController@GetCompaniesList']);
});
/**
 * Routes for resource commission
 */
$router->group(['prefix' => 'commissions', ['middleware' => 'role:Supplier']], function () use ($router) {
    $router->post('/', ['uses' => 'Commission\CommissionsController@addCommission']);
    $router->get('/shop', ['uses' => 'Commission\CommissionsController@getShopCommissions']);
    $router->get('/items', ['uses' => 'Commission\CommissionsController@getItemsCommissions']);
    // $router->put('/{id}', ['uses' => 'Commission\CommissionsController@updateCommission']);
    // $router->delete('/{id}', ['uses' => 'Commission\CommissionsController@DeleteCommission']);
    // $router->get('/{id}', ['uses' => 'Commission\CommissionsController@GetCommissionbySupplier']);
});

/**
 * Routes for resource siyou commission
 */
$router->group(['prefix' => 'siyoucommissions'], function () use ($router) {
    $router->post('/', ['uses' => 'SiyouCommissionsController@siyouCommission']);
    $router->post('/addSiyouCommission', ['uses' => 'SiyouCommissionsController@addSiyouCommission']);
    $router->get('/supplier', ['uses' => 'SiyouCommissionsController@GetsupplierCommission']);
    $router->get('/', ['uses' => 'SiyouCommissionsController@GetCommissionlist']);
    $router->put('/updateSiyouCommission', ['uses' => 'SiyouCommissionsController@updateSiyouCommission']);
    $router->delete('/{id}', ['uses' => 'SiyouCommissionsController@DeleteCommission']);
    $router->get('/GetsupplierswithCommission', ['uses' => 'SiyouCommissionsController@GetsupplierswithCommission']);
    $router->get('/{id}', ['uses' => 'SiyouCommissionsController@GetCommission']);
    $router->put('/UpdateDeposit/{supplier_id}', ['uses' => 'SiyouCommissionsController@UpdateDeposit']);
    $router->put('/{id}', ['uses' => 'SiyouCommissionsController@updateCommission']);
});

/**
 * Routes for resource funds
 */
$router->group(['prefix' => 'funds'], function () use ($router) {
    $router->group(['middleware' => 'role:Supplier'], function () use ($router) {
        $router->get('/transfert', ['uses' => 'fund\FundsController@transfertPayment']);
        $router->get('/check', ['uses' => 'fund\FundsController@checkPayment']);
        $router->get('/delivery', ['uses' => 'fund\FundsController@deliveryPayment']);
        $router->get('/paypal', ['uses' => 'fund\FundsController@paypalPayment']);
        $router->get('/card', ['uses' => 'fund\FundsController@creditCardPayment']);
    });
    // $router->group(['prefix' => '/salesmanager',['middleware' => 'role:SalesManager']], function () use ($router) {
    //     $router->get('/transfert', ['uses' => 'fund\SMFundsController@transfertPayment']);
    //     $router->get('/check', ['uses' => 'fund\SMFundsController@checkPayment']);
    //     $router->get('/delivery', ['uses' => 'fund\SMFundsController@deliveryPayment']);
    //     $router->get('/paypal', ['uses' => 'fund\SMFundsController@paypalPayment']);
    //     $router->get('/card', ['uses' => 'fund\SMFundsController@creditCardPayment']);
    // });
    $router->get('/{fund_id}', ['uses' => 'fund\FundsController@fundById']);
});
/**
 * Routes for resource catalogs
 */
$router->group(['prefix' => 'catalogs'], function () use ($router) {
    $router->post('/', ['uses' => 'Catalog\CatalogsController@AddCatalog']);
    $router->post('/AddProductstocatalog/{id}', ['uses' => 'Catalog\CatalogsController@AddProductstocatalog']);
    $router->get('/TopProductslist', ['uses' => 'Catalog\CatalogsController@TopProductslist']);
    $router->get('/supplier_Cataloglist', ['uses' => 'Catalog\CatalogsController@supplier_Cataloglist']);
    $router->get('/Supplier_showCatalog/{id}', ['uses' => 'Catalog\CatalogsController@Supplier_showCatalog']);
    $router->put('/{id}', ['uses' => 'Catalog\CatalogsController@UpdateCatalog']);
    $router->get('/', ['uses' => 'Catalog\CatalogsController@Cataloglist']);
    $router->delete('/{id}', ['uses' => 'Catalog\CatalogsController@DeleteCatalog']);
    $router->get('/{id}', ['uses' => 'Catalog\CatalogsController@getCatalogsBysupplier']);
});
/**
 * Routes for resource warehouse
 */
$router->group(['prefix' => 'warehouses'], function () use ($router) {
    $router->post('/', ['uses' => 'WarehousesController@addWarehouse']);
    $router->post('/AddProductstowarehouse/{id}', ['uses' => 'WarehousesController@AddProductstowarehouse']);
});

$router->group(['prefix' => 'companies'], function () use ($router) {
    $router->get('/Logistics', ['uses' => 'Logistics\CompaniesController@getmostusedcompany']);
});

/**
 * Routes for resource criteria
 */
$router->group(['prefix' => 'Criteria'], function () use ($router) {
    $router->group(['middleware' => 'role:Super_Admin'], function () use ($router) {
        $router->post('/', ['uses' => 'Criteria\CriteriaBasesController@addCriteria']);
        $router->post('/Unit', ['uses' => 'Criteria\CriteriaUnitsController@addCriteriaUnit']);
        $router->put('/{id}/Unit', ['uses' => 'Criteria\CriteriaUnitsController@updateUnit']);
        $router->put('/{id}', ['uses' => 'Criteria\CriteriaBasesController@updateCriteria']);
        $router->delete('/{id}/Unit', ['uses' => 'Criteria\CriteriaUnitsController@deleteUnit']);
        $router->delete('/{id}', ['uses' => 'Criteria\CriteriaBasesController@deleteCriteria']);
        $router->get('/Categories', ['uses' => 'Criteria\CriteriaBasesController@getCategoriesCriteria']);
    });
    $router->get('/List', ['uses' => 'Criteria\CriteriaBasesController@getCriteriaList']);
    $router->get('/', ['uses' => 'Criteria\CriteriaBasesController@getCriteria']);
    // $router->get('/category/{id}', ['uses' => 'Criteria\CriteriaBasesController@getCriteriaByCategory'])
    $router->get('/Unit', ['uses' => 'Criteria\CriteriaUnitsController@getUnit']);
});

/**
 * Routes for resource brand
 */
$router->group(['prefix' => 'Brand'], function () use ($router) {
    $router->group(['middleware' => 'role:Super_Admin'], function () use ($router) {
        $router->post('/', ['uses' => 'Brand\BrandsController@addBrand']);
        $router->post('/{id}', ['uses' => 'Brand\BrandsController@updateBrand']);
        $router->delete('/{id}', ['uses' => 'Brand\BrandsController@deleteBrand']);
    });
    $router->get('/List', ['uses' => 'Brand\BrandsController@getBrandList']);
    $router->get('/All', ['uses' => 'Brand\BrandsController@getMobileBrandList']);
    $router->get('/', ['uses' => 'Brand\BrandsController@getBrand']);
});
$router->post('/image', ['uses' => 'Product\ProductItemsController@uploadImages']);
$router->delete('/image/{id}', ['uses' => 'Product\ProductItemsController@deleteImage']);
// $router->put('/update', ['uses' => 'User\UsersController@updateUser']);


/**
 * Routes for resource supplier/slide
 */
$router->group(['prefix' => 'slides', ['middleware' => 'role:Supplier']], function () use ($router) {
    $router->post('/', ['uses' => 'Slide\SlidesController@uploadSlide']);
    $router->delete('/{id}', ['uses' => 'Slide\SlidesController@deleteSlide']);
    
});
$router->get('/slides', ['uses' => 'Slide\SlidesController@getSlides']);



/**
 * Routes for resource items/discounts
 */
$router->group(['prefix' => 'promotion', ['middleware' => 'role:Supplier']], function () use ($router) {
    $router->post('/', ['uses' => 'Product\DiscountsController@addPromotion']);
    $router->get('/', ['uses' => 'Product\DiscountsController@getDiscountItem']);
});

$router->group(['prefix' => 'dashboard'], function () use ($router) {
    $router->group(['middleware' => 'role:Supplier'], function () use ($router) {
        $router->get('/bestSeller', ['uses' => 'Dashboard\SuppliersController@bestSeller']);
        $router->get('/lastAdded', ['uses' => 'Dashboard\SuppliersController@lastAdded']);
        $router->get('/discount', ['uses' => 'Dashboard\SuppliersController@productDiscount']);
        $router->get('/shops', ['uses' => 'Dashboard\SuppliersController@getShopsList']);
    });
    $router->group(['middleware' => 'role:Shop_Owner'], function () use ($router) {
        $router->get('/shop', ['uses' => 'Dashboard\ShopsController@Dashboard']);
    });
});



/**
 * Routes for resource discount/-discount
 */
$router->group(['prefix' => 'discount', ['middleware' => 'role:Supplier']], function () use ($router) {
    $router->get('/list', ['uses' => 'Product\DiscountsController@getDiscountList']);
    // $router->get('/lastAdded', ['uses' => 'Dashboard\SuppliersController@lastAdded']);
    // $router->get('/discount', ['uses' => 'Dashboard\SuppliersController@productDiscount']);

});



/**
 * Routes for resource profile
 */
$router->group(['prefix' => 'profil'], function () use ($router) {
    $router->post('/cover/image', ['uses' => 'ProfilesController@uploadCoverImg']);
    $router->get('/', ['uses' => 'Dashboard\SuppliersController@MobileHomePage']);
    // $router->get('/lastAdded', ['uses' => 'Dashboard\SuppliersController@lastAdded']);
    // $router->get('/discount', ['uses' => 'Dashboard\SuppliersController@productDiscount']);

});
/**
 * Routes for resource discount/-discount
 */
$router->group(['prefix' => 'payment', ['middleware' => 'role:Supplier,Shop_Owner']], function () use ($router) {
    $router->get('/list', ['uses' => 'PaymentMethodsController@getPaymentMethodList']);
    // $router->get('/lastAdded', ['uses' => 'Dashboard\SuppliersController@lastAdded']);
    // $router->get('/discount', ['uses' => 'Dashboard\SuppliersController@productDiscount']);
});

$router->group(['prefix' => 'salesmanager',['middleware' => 'role:SalesManager']], function () use ($router) {
    $router->get('/suppliers', ['uses' => 'User\SalesmanagersController@getSupplierList']);
    $router->get('/shops', ['uses' => 'User\SalesmanagersController@getShopList']);
    $router->post('/position', ['uses' => 'User\SalesmanagersController@lastPosition']);
});

// $router->group(['middleware' => 'role:Supplier'], function () use ($router) {
$router->get('/position/history', ['uses' => 'User\SalesmanagersController@positionHistory']);


// });



//routes for funds available for supplier and super_admin



$router->group(['prefix' => 'payment/method'], function () use ($router) {
        $router->post('/', ['uses' => 'Purchase\FundsController@addPaymentMethod']);
	$router->get('/list/{id}', ['uses' => 'Purchase\FundsController@getPaymentMethodById']);
        $router->get('/', ['uses' => 'Purchase\FundsController@getPaymentMethods']);
	
        $router->post('/{id}', ['uses' => 'Purchase\FundsController@editPaymentMethod']);
        $router->delete('/{id}', ['uses' => 'Purchase\FundsController@deletePaymentMethod']);
    });
    $router->group(['prefix' => 'payment/fund'], function () use ($router) {
        $router->post('/', ['uses' => 'Purchase\FundsController@addFund']);
	$router->get('/list/{id}', ['uses' => 'Purchase\FundsController@getFundById']);
	$router->get('/list', ['uses' => 'Purchase\FundsController@getFunds1']);

        $router->get('/', ['uses' => 'Purchase\FundsController@getFunds']);
        $router->post('/{id}', ['uses' => 'Purchase\FundsController@updateFund']);
        $router->delete('/{id}', ['uses' => 'Purchase\FundsController@deleteFund']);
    });

// routes for inventories and warehouses available for supplier

$router->group(['middleware' => 'role:Supplier'], function () use ($router) {
    
    
    $router->group(['prefix' => 'inventory'], function () use ($router) {
        $router->post('/', ['uses' => 'Warehouse\InventoriesController@newInventory']);
        $router->post('/mobile', ['uses' => 'Warehouse\InventoriesController@newInventoryMobile']);
        $router->post('/batch/number', ['uses' => 'Warehouse\InventoriesController@generateBatchNumber']);
        // $router->post('/tostock', ['uses' => 'Warehouse\InventoriesController@productToStock']);
        $router->get('/list/{warehouse_id}', ['uses' => 'Warehouse\InventoriesController@getInventories']);
        $router->get('/{id}', ['uses' => 'Warehouse\InventoriesController@getInventory']);
        $router->put('/{id}', ['uses' => 'Warehouse\InventoriesController@updateInventory']);
        $router->post('/delete',['uses'=>'Warehouse\InventoriesController@deleteInventory']);
        $router->post('/stock',['uses'=>'Warehouse\InventoriesController@getInventoryProductList']);
    });
  $router->group(['prefix' => 'warehouses'], function () use ($router) {
                $router->post('/', ['uses' => 'Warehouse\WarehousesController@newWarehouse']);
                $router->get('/list', ['uses' => 'Warehouse\WarehousesController@getWarehouseList']);
		$router->get('/{id}', ['uses' => 'Warehouse\WarehousesController@getWarehouseById']);

                $router->post('/{id}', ['uses' => 'Warehouse\WarehousesController@updateWarehouse']);
                $router->delete('/{id}', ['uses' => 'Warehouse\WarehousesController@deleteWarehouse']);
                // $router->post('/batch/number', ['uses' => 'Warehouse\WarehousesController@generateBatchNumber']);
                // $router->post('/tostock', ['uses' => 'Warehouse\WarehousesController@productToStock']);
                
                // $router->put('/{level_id}', ['uses' => 'Warehouse\WarehousesController@updateLevel']);
            });

});


// wholesalers routes


$router->get('/wholesalers/list', ['uses' => 'User\WholesalersController@getWholesalerList']);
$router->get('/wholesalers/list/{id}', ['uses' => 'User\WholesalersController@getWholeSalerById']);
$router->post('/wholesalers', ['uses' => 'User\WholesalersController@addWholeSaler']);
//$router->get('/wholesalers', ['uses' => 'User\WholesalersController@getSupplierList2']);
$router->get('/wholesalers/all', ['uses' => 'User\WholesalersController@getwholesalers']);
$router->post('/wholesalers/assign', ['uses' => 'User\WholesalersController@addwholesalersToSupplier']);
$router->delete('/wholesalers/remove/{id}', ['uses' => 'User\WholesalersController@removewholeSalerFromSupplier']);










