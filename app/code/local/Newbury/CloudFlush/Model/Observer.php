<?php
class Newbury_CloudFlush_Model_Observer {

    public function flush() {

        if (Mage::getStoreConfig('cloudflush/config/enabled') == '1') {

            $config_email = Mage::getStoreConfig('cloudflush/config/email');
            $config_key = Mage::getStoreConfig('cloudflush/config/api_key');
            $zoneID = Mage::getStoreConfig('cloudflush/config/zone');

            if ($config_email && $config_key && $zoneID) {

                $email = 'X-Auth-Email: ' . $config_email;

                $key = 'X-Auth-Key: ' . $config_key;

                $headers = array($email, $key, 'Content-Type: application/json');

                $requestData = '{"purge_everything":true}';

                $response = $this->sendRequest('https://api.cloudflare.com/client/v4/zones/' . $zoneID . '/purge_cache', $headers, $requestData, 'DELETE');

                if ($response) {

                    $response = json_decode($response, true);

                    if (isset($response['success'])) {
                        if ($response['success'] == 'true') {
                            Mage::getSingleton('core/session')->addWarning('CloudFlare cache purging, this may take up to 30 seconds');
                        } else {
                            Mage::getSingleton('core/session')->addError('CloudFlare cache purge request failed, check CloudFlush.log for further details.');
                            Mage::log('Cache flushing failed:', null, 'CloudFlush.log', true);
                            Mage::log($response, null, 'CloudFlush.log', true);
                        }
                    } else {
                        Mage::getSingleton('core/session')->addError('Unexpected response returned from cloudflare, see CloudFlush.log for further details.');
                        Mage::log('Unexpected response when flushing cache:', null, 'CloudFlush.log', true);
                        Mage::log($response, null, 'CloudFlush.log', true);
                    }
                } else {
                    Mage::getSingleton('core/session')->addError('There was a problem communicating with the CloudFlare API, check CloudFlush.log for further details.');
                }
            } else {
                Mage::getSingleton('core/session')->addError('CloudFlush has not been configured correctly. Go to System > Configuration > CloudFlush and update all fields');
            }
        }
    }

    public function saveConfig() {

        $zoneID = Mage::getStoreConfig('cloudflush/config/zone');

        if (!$zoneID) {
            $config_email = Mage::getStoreConfig('cloudflush/config/email');
            $config_key = Mage::getStoreConfig('cloudflush/config/api_key');

            if ($config_email && $config_key) {
                
                $email = 'X-Auth-Email: ' . $config_email;

                $key = 'X-Auth-Key: ' . $config_key;

                $headers = array($email, $key, 'Content-Type: application/json');

                $response = $this->sendRequest('https://api.cloudflare.com/client/v4/zones/', $headers);

                $response = json_decode($response, true);

                if ($response) {

                    if (isset($response['result'])) {
                        Mage::getSingleton('core/session')->addNotice('The sites below are the ones associated with your CloudFlare account<br />Click the button for your site to save the settings for that site.');

                        foreach ($response['result'] as $item) {
                            Mage::getSingleton('core/session')->addNotice('<button onclick="document.getElementById(\'cloudflush_config_zone\').value = \'' . $item['id'] . '\';configForm.submit();">' . $item['name'] . '</button>');
                        }
                    }
                } else {
                    Mage::getSingleton('core/session')->addError('There was a problem communicating with the CloudFlare API, check CloudFlush.log for further details.');
                }
            } else {
                Mage::getSingleton('core/session')->addError('CloudFlush has not been configured correctly. Please ensure you have entered your email address and API key correctly');
            }
        }
    }

    protected function sendRequest($url, $headers = null, $postData = null, $verb = null)
    {

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($postData !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        if ($verb !== null) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
        }

        $response = curl_exec($ch);
        if(curl_error($ch))
        {
            Mage::log(curl_error($ch), null, 'CloudFlush.log', true);
            curl_close($ch);
            return false;
        } else {
            if ($response === '') {
                Mage::log('CURL_EXECUTED:EMPTY_RESPONSE', null, 'CloudFlush.log', true);
                curl_close($ch);
                return false;
            } else {
                curl_close($ch);
                return $response;
            }
        }
    }
}