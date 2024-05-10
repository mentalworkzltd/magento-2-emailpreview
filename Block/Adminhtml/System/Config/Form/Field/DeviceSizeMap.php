<?php
declare(strict_types=1);

namespace Mentalworkz\EmailPreview\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\DataObject;
use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;
use Mentalworkz\EmailPreview\Block\Adminhtml\System\Config\Form\Field\Type\Column\Enabled as EnabledFieldTypeColumn;

class DeviceSizeMap extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @var MentalworkzHelper
     */
    protected $mwzHelper;

    /**
     * @var EnabledFieldTypeColumn
     */
    private $enabledTypeRendererColumn;

    /**
     * DeviceSizeMap constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param MentalworkzHelper $mwzHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        MentalworkzHelper $mwzHelper,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->mwzHelper = $mwzHelper;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.
        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        $html = $this->_renderValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);
        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<td colspan="2" class="value" style="padding-top:20px;">';
        $html .= $this->_getElementHtml($element);
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _prepareToRender()
    {

        $this->addColumn('enabled', array(
            'label' => __('Enabled'),
            'renderer' => $this->getEnabledTypeRenderer(),
            'class' => 'enabled',
            'style' => 'width:50px'
        ));

        $this->addColumn('label', array(
            'label' => __('Label'),
            'class' => 'device_label',
        ));

        $this->addColumn('width', array(
            'label' => __('Width'),
            'class' => 'width',
            'style' => 'width:75px',
        ));

        $this->addColumn('height', array(
            'label' => __('Height'),
            'class' => 'height',
            'style' => 'width:75px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');

    }

    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return EnabledFieldTypeColumn
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getEnabledTypeRenderer()
    {
        if (!$this->enabledTypeRendererColumn) {
            $this->enabledTypeRendererColumn = $this->getLayout()->createBlock(
                EnabledFieldTypeColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->enabledTypeRendererColumn;
    }

}