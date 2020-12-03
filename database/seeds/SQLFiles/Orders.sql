
-- INSERT INTO `orders` (`created_at`, `updated_at`, `order_date`, `required_date`, `shipping_date`, `shipping_type`, `shipping_price`, `shipping_adresse`, `shipping_country`, `order_price`, `commission`, `supplier_id`, `shop_owner_id`, `statut_id`) VALUES
-- ('2020-01-06 15:52:24', '2020-01-06 15:52:24', '2020-01-06', '2020-03-20', '2020-03-23', 'poste', 35.54, 'tunis', 'tunisie', 330.00, 16.50, 1, 3, 1),
-- ('2020-01-06 15:53:12', '2020-01-06 15:53:12', '2020-01-06', '2020-03-20', '2020-03-23', 'poste', 35.54, 'tunis', 'tunisie', 330.00, 16.50, 1, 3, 1);

INSERT INTO `supplier_salesmanager_shop_owner` (`id`, `created_at`, `updated_at`, `supplier_id`, `salesmanager_id`, `shop_owner_id`, `commission_amount`) VALUES
(1, '2020-01-09 21:02:18', '2020-01-09 21:15:33', 2, 5, 4, 0),
(2, '2020-01-09 22:20:47', '2020-01-09 22:20:47', 2, 1, NULL, 0),
(3, '2020-01-24 15:55:06', '2020-01-26 00:58:53', 1, 5, 3, 20);
