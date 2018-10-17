<?php

namespace Mahu\SearchAlgolia\Domain\Model\Dto;


use TYPO3\CMS\Core\SingletonInterface;


class ExtensionConfiguration implements SingletonInterface
{

    /** @var string */
    protected $adminApiKey = '';

    /** @var string */
    protected $readOnlyApiKey = '';

    /** @var string */
    protected $appId = '';

    public function __construct()
    {
        $settings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('search_algolia');

        $this->setAdminApiKey($settings['adminApiKey']);
        $this->setReadOnlyApiKey($settings['readOnlyApiKey']);
        $this->setAppId($settings['appId']);
    }

    /**
     * @return string
     */
    public function getAdminApiKey(): string
    {
        return $this->adminApiKey;
    }

    /**
     * @param string $adminApiKey
     */
    public function setAdminApiKey(string $adminApiKey)
    {
        $this->adminApiKey = $adminApiKey;
    }

    /**
     * @return string
     */
    public function getReadOnlyApiKey(): string
    {
        return $this->readOnlyApiKey;
    }

    /**
     * @param string $readOnlyApiKey
     */
    public function setReadOnlyApiKey(string $readOnlyApiKey)
    {
        $this->readOnlyApiKey = $readOnlyApiKey;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId)
    {
        $this->appId = $appId;
    }












}
