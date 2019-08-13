<?php
class Channelbrain_Catalog_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    public function getTierPrice($qty=null, $product)
    {
		$allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('tier_price');
		$today=date("Y-m-d");
		if(Mage::getStoreConfig("mypackage/settings/wds_tier_price")){
		
		
		//print "<pre>";
		//print_r($prices);die;
 		if (is_null($prices)) {
			
            if ($attribute = $product->getResource()->getAttribute('tier_price')) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('tier_price');
            }
        }
         
        if (is_null($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
				
                return $product->getPrice();
            }
			return array(array(
                'price'         => $product->getPrice(),
                'website_price' => $product->getPrice(),
                'price_qty'     => 1,
                'cust_group'    => $allGroups,
            ));
        }
 
        $custGroup = $this->_getCustomerGroupId($product);
		if ($qty) {
			
			$prevQty = 1;
            $prevPrice = $product->getPrice();
            $prevGroup = $allGroups;
            foreach ($prices as $price) {
				if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty']) {
                    // tier is higher than product qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty) {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups) {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
 
                $prevPrice  = $price['website_price'];
                $prevQty    = $price['price_qty'];
				
				if(isset($price['from_date']) && isset($price['to_date']))
				{
					
					if ($today>=$price['from_date'] && $today<=$price['to_date'])
					{
						$prevPrice=$price['price'];
					}
					else
					{
						$prevPrice=$product->getPrice();
					}
				}
				else
				{
					$prevPrice=$product->getPrice();
				}
                $prevGroup  = $price['cust_group'];
            }
			//echo $prevPrice;die;
            return $prevPrice;
        } else {
            foreach ($prices as $i=>$price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    unset($prices[$i]);
                }
            }
        }
		if(isset($prices[0]['from_date']) && isset($prices[0]['to_date']))
		{
			if ($today>=$prices[0]['from_date'] && $today<=$prices[0]['to_date'])
			{
				return ($prices) ? $prices : array();
			}
		}
	  }
	}
}
?>