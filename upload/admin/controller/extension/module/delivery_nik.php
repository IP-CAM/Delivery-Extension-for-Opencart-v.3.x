<?php
class ControllerExtensionModuleDeliveryNik extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/delivery_nik');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
        $this->load->model('extension/module/delivery_nik');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_delivery_nik', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$this->getList();
	}

    public function addDelivery() {
        $this->load->language('extension/module/delivery_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/delivery_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDeliveryForm()) {
//            echo "<pre>";
//            print_r($this->request->post);
//            echo "</pre>";
            $this->model_extension_module_delivery_nik->addDelivery($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function editDelivery() {
        $this->load->language('extension/module/delivery_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/delivery_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDeliveryForm()) {
            $this->model_extension_module_delivery_nik->editDelivery($this->request->get['delivery_id'],$this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function deleteDelivery() {
        $this->load->language('extension/module/delivery_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/delivery_nik');

        if (isset($this->request->get['delivery_id']) && $this->validateDelete()) {
            $this->model_extension_module_delivery_nik->deleteDelivery($this->request->get['delivery_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'], true);
        $data['addDelivery'] = $this->url->link('extension/module/delivery_nik/addDelivery', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_delivery_nik_status'])) {
            $data['module_delivery_nik_status'] = $this->request->post['module_delivery_nik_status'];
        } else {
            $data['module_delivery_nik_status'] = $this->config->get('module_delivery_nik_status');
        }

        $results = $this->model_extension_module_delivery_nik->getDeliveries();

        foreach ($results as $result) {
            $data['deliveries'][] = array(
                'delivery_id'   => $result['delivery_id'],
                'name'          => $result['name'],
                'sort_order'    => $result['sort_order'],
                'edit'          => $this->url->link('extension/module/delivery_nik/editDelivery', 'user_token=' . $this->session->data['user_token'] . '&delivery_id=' . $result['delivery_id'], true),
                'delete'        => $this->url->link('extension/module/delivery_nik/deleteDelivery', 'user_token=' . $this->session->data['user_token'] . '&delivery_id=' . $result['delivery_id'], true)
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/delivery_nik', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['delivery_id']) ? $this->language->get('text_add_delivery') : $this->language->get('text_edit_delivery');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['delivery_id'])) {
            $data['action'] = $this->url->link('extension/module/delivery_nik/addDelivery', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/delivery_nik/editDelivery', 'user_token=' . $this->session->data['user_token'] . '&delivery_id=' . $this->request->get['delivery_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/delivery_nik', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['delivery_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $delivery_info = $this->model_extension_module_delivery_nik->getDelivery($this->request->get['delivery_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['delivery_description'])) {
            $data['delivery_description'] = $this->request->post['delivery_description'];
        } elseif (isset($this->request->get['delivery_id'])) {
            $data['delivery_description'] = $this->model_extension_module_delivery_nik->getDeliveryDescription($this->request->get['delivery_id']);
        } else {
            $data['delivery_description'] = array();
        }

        if (isset($this->request->post['cost'])) {
            $data['cost'] = $this->request->post['cost'];
        } elseif (!empty($delivery_info)) {
            $data['cost'] = $delivery_info['cost'];
        } else {
            $data['cost'] = '';
        }

        $this->load->model('localisation/tax_class');

        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        if (isset($this->request->post['geo_zone_id'])) {
            $data['tax_class_id'] = $this->request->post['tax_class_id'];
        } elseif (!empty($delivery_info)) {
            $data['tax_class_id'] = $delivery_info['tax_class_id'];
        } else {
            $data['tax_class_id'] = 0;
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['geo_zone_id'])) {
            $data['geo_zone_id'] = $this->request->post['geo_zone_id'];
        } elseif (!empty($delivery_info)) {
            $data['geo_zone_id'] = $delivery_info['geo_zone_id'];
        } else {
            $data['geo_zone_id'] = 0;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($delivery_info)) {
            $data['sort_order'] = $delivery_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($delivery_info)) {
            $data['status'] = $delivery_info['status'];
        } else {
            $data['status'] = true;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/delivery_form_nik', $data));
    }

    public function install() {
        if ($this->user->hasPermission('modify', 'extension/module/delivery_nik')) {
            $this->load->model('extension/module/delivery_nik');

            $this->model_extension_module_delivery_nik->install();
        }
    }

    public function uninstall() {
        if ($this->user->hasPermission('modify', 'extension/module/delivery_nik')) {
            $this->load->model('extension/module/delivery_nik');

            $this->model_extension_module_delivery_nik->uninstall();
        }
    }


    protected function validateDeliveryForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/delivery_nik')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['delivery_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/delivery_nik')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/delivery_nik')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}