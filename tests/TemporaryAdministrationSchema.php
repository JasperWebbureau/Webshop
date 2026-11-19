<?php

/**
 * Session-local Admin schema for rollback integration tests. Never creates permanent tables.
 * Unique external-reference keys mirror the Admin entity contracts used by the adapter.
 */
final class TemporaryAdministrationSchema
{
public static function create(PDO $pdo): void
{
    $tables = [
        'admin_core_number_sequence' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `sequence_key` VARCHAR(64) NOT NULL, `period_key` VARCHAR(32) NOT NULL, `current_value` BIGINT NOT NULL, UNIQUE KEY `external_sequence` (`tenant_id`, `sequence_key`, `period_key`)',
        'admin_customer' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `display_name` VARCHAR(255) NOT NULL, `company_name` VARCHAR(255) NULL, `registration_number` VARCHAR(64) NULL, `tax_number` VARCHAR(64) NULL, `status` VARCHAR(16) NOT NULL, `notes` TEXT NULL, `source` VARCHAR(64) NULL, `external_id` VARCHAR(128) NULL, `created_at` BIGINT NOT NULL, `updated_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `external_ref` (`tenant_id`, `source`, `external_id`)',
        'admin_customer_contact' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `customer_id` BIGINT NOT NULL, `name` VARCHAR(255) NOT NULL, `email` VARCHAR(255) NULL, `phone` VARCHAR(64) NULL, `role` VARCHAR(128) NULL, `is_primary` TINYINT NOT NULL, `position` INT NOT NULL, `created_at` BIGINT NOT NULL, `updated_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `customer_position` (`tenant_id`, `customer_id`, `position`)',
        'admin_customer_address' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `customer_id` BIGINT NOT NULL, `address_type` VARCHAR(16) NOT NULL, `label` VARCHAR(128) NULL, `addressee` VARCHAR(255) NULL, `line1` VARCHAR(255) NOT NULL, `line2` VARCHAR(255) NULL, `postal_code` VARCHAR(32) NOT NULL, `city` VARCHAR(128) NOT NULL, `region` VARCHAR(128) NULL, `country_code` VARCHAR(2) NOT NULL, `is_primary` TINYINT NOT NULL, `position` INT NOT NULL, `created_at` BIGINT NOT NULL, `updated_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `customer_position` (`tenant_id`, `customer_id`, `address_type`, `position`)',
        'admin_invoice' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `invoice_number` VARCHAR(64) NULL, `invoice_sequence_value` BIGINT NULL, `status` VARCHAR(20) NOT NULL, `payment_status` VARCHAR(24) NOT NULL, `currency` VARCHAR(3) NOT NULL, `customer_reference` VARCHAR(128) NULL, `customer_snapshot` TEXT NOT NULL, `billing_address_snapshot` TEXT NOT NULL, `issue_date` VARCHAR(10) NOT NULL, `due_date` VARCHAR(10) NOT NULL, `finalized_at` BIGINT NULL, `notes` TEXT NULL, `source` VARCHAR(64) NULL, `external_id` VARCHAR(128) NULL, `net_total_minor` BIGINT NOT NULL, `tax_total_minor` BIGINT NOT NULL, `gross_total_minor` BIGINT NOT NULL, `tax_summary_snapshot` TEXT NOT NULL, `created_at` BIGINT NOT NULL, `updated_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `invoice_number` (`tenant_id`, `invoice_number`), UNIQUE KEY `external_ref` (`tenant_id`, `source`, `external_id`)',
        'admin_invoice_line' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `invoice_id` BIGINT NOT NULL, `description` TEXT NOT NULL, `quantity_scaled` BIGINT NOT NULL, `unit` VARCHAR(32) NOT NULL, `unit_price_minor` BIGINT NOT NULL, `tax_rate_basis_points` INT NOT NULL, `tax_category` VARCHAR(32) NOT NULL, `catalog_item_public_id` VARCHAR(64) NULL, `position` INT NOT NULL, `net_total_minor` BIGINT NOT NULL, `tax_total_minor` BIGINT NOT NULL, `gross_total_minor` BIGINT NOT NULL, `created_at` BIGINT NOT NULL, `updated_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `invoice_position` (`tenant_id`, `invoice_id`, `position`)',
        'admin_payment' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `payment_type` VARCHAR(16) NOT NULL, `amount_minor` BIGINT NOT NULL, `currency` VARCHAR(3) NOT NULL, `booked_on` VARCHAR(10) NOT NULL, `description` VARCHAR(255) NULL, `reference` VARCHAR(255) NULL, `source` VARCHAR(64) NOT NULL, `external_id` VARCHAR(128) NULL, `created_at` BIGINT NOT NULL, `updated_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `external_ref` (`tenant_id`, `source`, `external_id`)',
        'admin_payment_allocation' => '`id` BIGINT AUTO_INCREMENT PRIMARY KEY, `tenant_id` VARCHAR(64) NOT NULL, `public_id` VARCHAR(64) NOT NULL, `payment_id` BIGINT NOT NULL, `target_type` VARCHAR(32) NOT NULL, `target_public_id` VARCHAR(64) NOT NULL, `amount_minor` BIGINT NOT NULL, `allocated_at` BIGINT NOT NULL, `created_at` BIGINT NOT NULL, UNIQUE KEY `public_ref` (`tenant_id`, `public_id`), UNIQUE KEY `payment_target` (`tenant_id`, `payment_id`, `target_type`, `target_public_id`)',
    ];

    foreach ($tables as $name => $columns) {
        $pdo->exec('CREATE TEMPORARY TABLE `' . $name . '` (' . $columns . ') ENGINE=InnoDB');
    }
}

}
