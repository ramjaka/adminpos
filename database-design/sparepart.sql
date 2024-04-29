/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     3/9/2024 11:31:46 PM                         */
/*==============================================================*/

drop table if exists bank_account;

drop table if exists debt;

drop table if exists debt_installment;

drop table if exists member;

drop table if exists mutation;

drop table if exists product;

drop table if exists promotion;

drop table if exists purchase;

drop table if exists purchase_cart;

drop table if exists purchase_detail;

drop table if exists receivable;

drop table if exists receivable_installment;

drop table if exists sales;

drop table if exists sales_cart;

drop table if exists sales_detail;

drop table if exists spending;

drop table if exists stock;

drop table if exists supplier;

drop table if exists user;

/*==============================================================*/
/* Table: bank_account                                          */
/*==============================================================*/
create table bank_account
(
   bank_account_id      int not null  comment '',
   bank_account_balance varchar(255)  comment '',
   bank_account_number  varchar(255)  comment '',
   bank_account_holder  varchar(100)  comment '',
   bank_account_update_at varchar(255)  comment '',
   bank_account_created_at varchar(255)  comment '',
   primary key (bank_account_id)
);

/*==============================================================*/
/* Table: debt                                                  */
/*==============================================================*/
create table debt
(
   purchase_id          varchar(255)  comment '',
   debt_installment_id  varchar(255)  comment '',
   debt_id              varchar(255) not null  comment '',
   debt_total           varchar(255)  comment '',
   fulfillment_status   varchar(25) not null  comment '',
   debt_udpated_by      varchar(255)  comment '',
   debt_created_by      varchar(255)  comment '',
   debt_updated_at      varchar(255)  comment '',
   debt_created_at      varchar(255) not null  comment '',
   primary key (debt_id)
);

/*==============================================================*/
/* Table: debt_installment                                      */
/*==============================================================*/
create table debt_installment
(
   debt_installment_id  varchar(255) not null  comment '',
   debt_id              varchar(255)  comment '',
   installment_amount   varchar(255)  comment '',
   bank_account_id      varchar(255)  comment '',
   debt_installment_updated_by varchar(255)  comment '',
   debt_installment_created_by varchar(255)  comment '',
   debt_installment_updated_at varchar(255)  comment '',
   debt_installment_created_at varchar(255)  comment '',
   primary key (debt_installment_id)
);

/*==============================================================*/
/* Table: member                                                */
/*==============================================================*/
create table member
(
   member_id            varchar(255) not null  comment '',
   member_first_name    varchar(50)  comment '',
   member_last_name     varchar(100)  comment '',
   member_phone         varchar(20)  comment '',
   member_email         varchar(100)  comment '',
   member_address       varchar(255)  comment '',
   member_status        char(10)  comment '',
   member_updated_by    varchar(255)  comment '',
   member_created_by    varchar(255) not null  comment '',
   member_updated_at    varchar(255)  comment '',
   member_created_at    varchar(255) not null  comment ''
);

/*==============================================================*/
/* Table: mutation                                              */
/*==============================================================*/
create table mutation
(
   mutation_id          char(10) not null  comment '',
   mutation_description char(10)  comment '',
   debt                 char(10)  comment '',
   credit               char(10)  comment '',
   mutation_created_at  varchar(255) not null  comment ''
);

/*==============================================================*/
/* Table: product                                               */
/*==============================================================*/
create table product
(
   product_sku          varchar(35) not null  comment '',
   product_name         varchar(255) not null  comment '',
   product_weight       varchar(10)  comment '',
   product_color        varchar(15)  comment '',
   product_category     varchar(255)  comment '',
   product_description  text  comment '',
   product_media_1      text  comment '',
   product_media_2      text  comment '',
   product_media_3      text  comment '',
   product_media_4      text  comment '',
   product_media_5      text  comment '',
   product_price        varchar(255)  comment '',
   product_status       varchar(10) not null  comment '',
   product_updated_by   varchar(255)  comment '',
   product_created_by   varchar(255) not null  comment '',
   product_updated_at   varchar(255)  comment '',
   product_created_at   varchar(255) not null  comment '',
   primary key (product_sku)
);

/*==============================================================*/
/* Table: promotion                                             */
/*==============================================================*/
create table promotion
(
   promotion_id         int not null  comment '',
   promotion_name       varchar(255) not null  comment '',
   promotion_value      varchar(255) not null  comment '',
   promotion_status     varchar(10) not null  comment '',
   promotion_updated_by varchar(255)  comment '',
   promotion_created_by varchar(255) not null  comment '',
   promotion_updated_at varchar(255)  comment '',
   promotion_created_at varchar(255) not null  comment '',
   primary key (promotion_id, promotion_name)
);

/*==============================================================*/
/* Table: purchase                                              */
/*==============================================================*/
create table purchase
(
   purchase_id          varchar(255) not null  comment '',
   supplier_id          int  comment '',
   bank_account_id      varchar(255)  comment '',
   fulfillment_status   varchar(25)  comment '',
   purchase_maturity    varchar(255)  comment '',
   purchase_total       varchar(255)  comment '',
   purchase_updated_by  char(10)  comment '',
   purchase_created_by  char(10) not null  comment '',
   purchase_updated_at  char(10)  comment '',
   purchase_created_at  char(10) not null  comment '',
   primary key (purchase_id)
);

/*==============================================================*/
/* Table: purchase_cart                                         */
/*==============================================================*/
create table purchase_cart
(
   purchase_cart_id     varchar(255) not null  comment '',
   product_sku          varchar(35)  comment '',
   product_qty          int  comment '',
   purchase_price       varchar(255)  comment '',
   selling_price        varchar(255)  comment '',
   description          text  comment '',
   primary key (purchase_cart_id)
);

/*==============================================================*/
/* Table: purchase_detail                                       */
/*==============================================================*/
create table purchase_detail
(
   purchase_id          varchar(255) not null  comment '',
   product_sku          varchar(255)  comment '',
   product_qty          int  comment '',
   purchase_price       varchar(255)  comment '',
   selling_price        varchar(255)  comment '',
   description          text  comment '',
   primary key (purchase_id)
);

/*==============================================================*/
/* Table: receivable                                            */
/*==============================================================*/
create table receivable
(
   receviable_id        varchar(255) not null  comment '',
   sales_id             varchar(255)  comment '',
   receviable_installment_id char(10)  comment '',
   fulfillment_status   varchar(25)  comment '',
   recevibale_total     varchar(255)  comment '',
   receivable_updated_by varchar(255)  comment '',
   receivable_created_by varchar(255)  comment '',
   recevibale_updated_at varchar(255)  comment '',
   receivable_created_at varchar(255) not null  comment '',
   primary key (receviable_id)
);

/*==============================================================*/
/* Table: receivable_installment                                */
/*==============================================================*/
create table receivable_installment
(
   receviable_installment_id char(10) not null  comment '',
   receviable_id        varchar(255)  comment '',
   installment_amount   varchar(255)  comment '',
   bank_account_id      varchar(255)  comment '',
   receviabl_installment_updated_by varchar(255)  comment '',
   receviablet_installment_created_by varchar(255)  comment '',
   receviable_installment_updated_at varchar(255)  comment '',
   receviable_installment_created_at varchar(255)  comment '',
   primary key (receviable_installment_id)
);

/*==============================================================*/
/* Table: sales                                                 */
/*==============================================================*/
create table sales
(
   sales_id             varchar(255) not null  comment '',
   member_id            varchar(255)  comment '',
   bank_account_id      int  comment '',
   promotion_name       varchar(255)  comment '',
   sales_maturity       varchar(255)  comment '',
   sales_total          varchar(255)  comment '',
   sales_updated_by     varchar(255)  comment '',
   sales_created_by     varchar(255) not null  comment '',
   sales_updated_at     varchar(255)  comment '',
   sales_created_at     varchar(255) not null  comment '',
   primary key (sales_id)
);

/*==============================================================*/
/* Table: sales_cart                                            */
/*==============================================================*/
create table sales_cart
(
   sales_cart_id        varchar(255) not null  comment '',
   product_sku          char(10)  comment '',
   product_qty          char(10)  comment '',
   primary key (sales_cart_id)
);

/*==============================================================*/
/* Table: sales_detail                                          */
/*==============================================================*/
create table sales_detail
(
   sales_id             varchar(255)  comment '',
   product_sku          varchar(35)  comment '',
   product_qty          int  comment '',
   sales_detail_updated_by varchar(255)  comment '',
   sales_detail_created_by varchar(255) not null  comment '',
   sales_detail_updated_at varchar(255)  comment '',
   sales_detail_created_at varchar(255) not null  comment ''
);

/*==============================================================*/
/* Table: spending                                              */
/*==============================================================*/
create table spending
(
   spending_id          varchar(255) not null  comment '',
   bank_account_id      char(10)  comment '',
   spending_name        varchar(255)  comment '',
   spending_description text  comment '',
   spending_total       varchar(255) not null  comment '',
   spending_fulfillment varchar(255)  comment '',
   spending_created_by  varchar(255) not null  comment '',
   spending_created_at  varchar(255) not null  comment '',
   primary key (spending_id)
);

/*==============================================================*/
/* Table: stock                                                 */
/*==============================================================*/
create table stock
(
   product_sku          varchar(255) not null  comment '',
   purchase__price      varchar(255)  comment '',
   selling_price        varchar(255)  comment '',
   stock_qty            int  comment '',
   stock_updated_by     varchar(255)  comment '',
   stock_created_by     varchar(255) not null  comment '',
   stock_updated_at     varchar(255)  comment '',
   stock_created_at     varchar(255) not null  comment '',
   primary key (product_sku)
);
/*==============================================================*/
/* Table: supplier                                              */
/*==============================================================*/
create table supplier
(
   supplier_id          int not null  comment '',
   supplier_updated_by  varchar(255)  comment '',
   supplier_created_by  varchar(255) not null  comment '',
   supplier_updated_at  varchar(255)  comment '',
   supplier_created_at  varchar(255) not null  comment '',
   primary key (supplier_id)
);

/*==============================================================*/
/* Table: user                                                  */
/*==============================================================*/
create table user
(
   user_id              int not null  comment '',
   first_name           varchar(25)  comment '',
   last_name            varchar(25)  comment '',
   usrename             varchar(50) not null  comment '',
   phone                varchar(20) not null  comment '',
   purchase_cart_id     varchar(255)  comment '',
   sales_cart_id        varchar(255)  comment '',
   access               varchar(255)  comment '',
   password             varchar(255)  comment '',
   user_updated_by      varchar(255)  comment '',
   user_created_by      varchar(255) not null  comment '',
   user_updated_at      varchar(255)  comment '',
   user_created_at      varchar(255) not null  comment '',
   primary key (user_id, usrename, phone)
);