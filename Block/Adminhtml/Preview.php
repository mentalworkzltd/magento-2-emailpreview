<?php
/**
 * Mentalworkz
 *
 * @category    Mentalworkz
 * @package     Mentalworkz_EmailPreview
 * @copyright   Copyright (c) Mentalworkz (https://www.mentalworkz.co.uk/)
 * @author      Shaun Clifford
 */
declare(strict_types=1);

namespace Mentalworkz\EmailPreview\Block\Adminhtml;

use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;

class Preview extends \Magento\Backend\Block\Page
{

    /**
     * @var MentalworkzHelper
     */
    private $mwzHelper;

    /**
     * Preview constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param MentalworkzHelper $mwzHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        MentalworkzHelper $mwzHelper,
        array $data = []
    ) {
        parent::__construct($context, $localeResolver, $data);
        $this->mwzHelper = $mwzHelper;
    }

    /**
     * @return array
     */
    public function getResponsiveDimensions (): array
    {
        return $this->mwzHelper->getPreviewDevices();
    }

}
