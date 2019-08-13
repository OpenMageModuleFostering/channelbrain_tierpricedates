<?php
class Channelbrain_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
 
{
    public function loadProductPrices($product, $attribute)
    {
		echo "rtrtrt";
		exit;
		echo "Working";
        //add new column name to the sql
        $select = $this->_getReadAdapter()->select()
            ->from($this>getMainTable(), array(
                'website_id', 'all_groups', 'cust_group' => 'customer_group_id',
                'price_qty' => 'qty', 'price' => 'value', 'from_date', 'to_date',
            ))
            ->where('entity_id=?', $product->getId())
            ->order('qty');
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id=?', 0);
        }
        else {
            if ($storeId = $product->getStoreId()) {
                $select->where('website_id IN (?)', array(0, Mage::app()->getStore($storeId)->getWebsiteId()));
            }
        }
        return $this->_getReadAdapter()->fetchAll($select);
    }
	
	protected function _loadPriceDataColumns($columns){
		
		$columns = parent::_loadPriceDataColumns($columns);
        $columns['price_qty'] = 'qty';
		$columns['from_date'] = 'from_date';
		$columns['to_date'] = 'to_date';
		return $columns;
    }

	public function insertProductPrice($product, $data){
		$fdate=explode("/",$data['from_date']);
		$tdate=explode("/",$data['to_date']);
		
		$data['from_date']=$fdate[2]."-".$fdate[0]."-".$fdate[1];
		$data['to_date']=$tdate[2]."-".$tdate[0]."-".$tdate[1];
		
		$priceObject = new Varien_Object($data);
        $priceObject->setEntityId($product->getId());
		return $this->savePriceData($priceObject);
    }
}
?>