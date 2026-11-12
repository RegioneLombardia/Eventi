<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 */

namespace Zend\Feed\Reader\Extension\CreativeCommons;

use Zend\Feed\Reader\Extension;

class Feed extends Extension\AbstractFeed
{
    /**
     * Get the entry license
     *
     * @param int $index
     * @return string|null
     */
    public function getLicense($index = 0)
    {
        $licenses = $this->getLicenses();

        if (isset($licenses[$index])) {
            return $licenses[$index];
        }

        return;
    }

    /**
     * Get the entry licenses
     *
     * @return array
     */
    public function getLicenses()
    {
        $name = 'licenses';
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $licenses = [];
        $list = $this->xpath->evaluate('channel/cc:license');

        if ($list->length) {
            foreach ($list as $license) {
                $licenses[] = $license->nodeValue;
            }

            $licenses = array_unique($licenses);
        }

        $this->data[$name] = $licenses;

        return $this->data[$name];
    }

    /**
     * Register Creative Commons namespaces
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('cc', 'http://backend.userland.com/creativeCommonsRssModule');
    }
}
