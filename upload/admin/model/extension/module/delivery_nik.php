<?php
class ModelExtensionModuleDeliveryNik extends Model {
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "delivery` (
			`delivery_id` INT(11) NOT NULL AUTO_INCREMENT,
			`cost` VARCHAR(32) NOT NULL,
			`tax_class_id` INT(11) NOT NULL,
			`geo_zone_id` INT(11) NOT NULL,
			`sort_order` INT(11) NOT NULL,
			`status` INT(11) NOT NULL,
			PRIMARY KEY (`delivery_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "delivery_description` (
			`delivery_id` INT(11) NOT NULL,
			`language_id` INT(11) NOT NULL,
			`name` VARCHAR(75) NOT NULL,
			PRIMARY KEY (`delivery_id`, `language_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "delivery`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "delivery_description`");
    }

    public function addDelivery($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "delivery SET `cost` = '" . $this->db->escape($data['cost']) . "', `tax_class_id` = '" . (int)$data['tax_class_id'] . "', `geo_zone_id` = '" . (int)$data['geo_zone_id'] . "', `sort_order` = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

        $delivery_id = $this->db->getLastId();

        foreach ($data['delivery_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "delivery_description SET delivery_id = '" . (int)$delivery_id . "', language_id = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");
        }

        $this->cache->delete('delivery');

        return $delivery_id;
    }

    public function editDelivery($delivery_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "delivery SET `cost` = '" . $this->db->escape($data['cost']) . "', `tax_class_id` = '" . (int)$data['tax_class_id'] . "', `geo_zone_id` = '" . (int)$data['geo_zone_id'] . "', `sort_order` = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE delivery_id = '" . (int)$delivery_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "delivery_description WHERE delivery_id = '" . (int)$delivery_id . "'");

        foreach ($data['delivery_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "delivery_description SET delivery_id = '" . (int)$delivery_id . "', language_id = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");
        }

        $this->cache->delete('delivery');
    }

    public function deleteDelivery($delivery_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "delivery` WHERE delivery_id = '" . (int)$delivery_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "delivery_description` WHERE delivery_id = '" . (int)$delivery_id . "'");

        $this->cache->delete('delivery');
    }

    public function getDelivery($delivery_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery WHERE delivery_id = '" . (int)$delivery_id . "'");

        return $query->row;
    }

    public function getDeliveryDescription($delivery_id) {
        $delivery_description_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_description WHERE delivery_id = '" . (int)$delivery_id . "'");

        foreach ($query->rows as $result) {
            $delivery_description_data[$result['language_id']] = array(
                'name' => $result['name'],
            );
        }

        return $delivery_description_data;
    }

    public function getDeliveries($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "delivery d LEFT JOIN " . DB_PREFIX . "delivery_description dd ON (d.delivery_id = dd.delivery_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $sort_data = array(
            'dd.name',
            'd.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY dd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
}
