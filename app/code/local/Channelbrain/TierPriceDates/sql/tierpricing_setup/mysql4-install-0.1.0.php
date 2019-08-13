<?php
    $installer = $this;
    $installer->startSetup();
    $installer->run("ALTER TABLE {$this->getTable('catalog_product_entity_tier_price')}  ADD from_date DATE NOT NULL AFTER qty, ADD to_date DATE NOT NULL AFTER from_date;");
    $installer->endSetup();
?>	