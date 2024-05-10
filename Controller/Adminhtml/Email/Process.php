<?php
declare(strict_types=1);

namespace Mentalworkz\EmailPreview\Controller\Adminhtml\Email;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;
use Mentalworkz\EmailPreview\Model\TemplateData;
use Mentalworkz\EmailPreview\Model\FetchEntity;
use Magento\Email\Model\TemplateFactory;
use Magento\Email\Model\ResourceModel\Template as TemplateResource;
use Magento\Email\Model\Template\Config as EmailConfig;
use Magento\Email\Model\BackendTemplate;
use Magento\Framework\Filter\Input\MaliciousCode;
use Zend\Mail\Message as ZendMessage;
use Magento\Framework\Serialize\Serializer\Json;
use Mentalworkz\EmailPreview\Logger\Logger as MwzLogger;
use Magento\Framework\Escaper;
use Magento\Store\Model\App\Emulation;

class Process extends \Magento\Backend\App\Action
{

    /**
     * @var resultFactory
     */
    protected $resultFactory;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var MentalworkzHelper
     */
    protected $mwzhelper;

    /**
     * @var TemplateData
     */
    protected $templateData;

    /**
     * @var FetchEntity
     */
    protected $fetchEntity;

    /**
     * @var TemplateFactory
     */
    protected $emailFactory;

    /**
     * @var TemplateResource
     */
    protected $templateResource;

    /**
     * @var EmailConfig
     */
    protected $emailConfig;

    /**
     * @var BackendTemplate
     */
    protected $backendTemplate;

    /**
     * @var MaliciousCode
     */
    protected $maliciousCode;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var string
     */
    protected $profilerName = 'email_template_proccessing';

    /**
     * @var MwzLogger
     */
    protected $mwzLogger;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * Process constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param ResultFactory $resultFactory
     * @param Validator $formKeyValidator
     * @param TemplateData $templateData
     * @param FetchEntity $fetchEntity
     * @param MentalworkzHelper $mwzhelper
     * @param TemplateFactory $emailFactory
     * @param TemplateResource $templateResource
     * @param EmailConfig $emailConfig
     * @param BackendTemplate $backendTemplate
     * @param MaliciousCode $maliciousCode
     * @param Json $serializer
     * @param MwzLogger $mwzLogger
     * @param Escaper $escaper
     * @param Emulation $emulation
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ResultFactory $resultFactory,
        Validator $formKeyValidator,
        TemplateData $templateData,
        FetchEntity $fetchEntity,
        MentalworkzHelper $mwzhelper,
        TemplateFactory $emailFactory,
        TemplateResource $templateResource,
        EmailConfig $emailConfig,
        BackendTemplate $backendTemplate,
        MaliciousCode $maliciousCode,
        Json $serializer,
        MwzLogger $mwzLogger,
        Escaper $escaper,
        Emulation $emulation
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->templateData = $templateData;
        $this->fetchEntity = $fetchEntity;
        $this->mwzhelper = $mwzhelper;
        $this->emailFactory = $emailFactory;
        $this->templateResource = $templateResource;
        $this->emailConfig = $emailConfig;
        $this->backendTemplate = $backendTemplate;
        $this->maliciousCode = $maliciousCode;
        $this->mwzLogger = $mwzLogger;
        $this->serializer = $serializer;
        $this->escaper = $escaper;
        $this->emulation = $emulation;
    }

    public function execute()
    {
        try {

            $hasError = false;
            $emailHtml = "";
            $errorMessages = [];
            $successMessages = [];
            $response = ['error' => true, 'messages' => [], 'template_id' => '','template_type' => '', 'template_code' => '', 'html' => ''];

            if ($this->mwzhelper->getPreviewSendEnabled()) {

                \Magento\Framework\Profiler::start($this->profilerName);

                $storeId = (int)$this->getRequest()->getParam('store_id', 1);
                $templateId = $this->getRequest()->getParam('template_id');
                $templateType = $this->getRequest()->getParam('template_type', 'custom');
                $emailAddress = $this->getRequest()->getParam('email_address', null);
                $entityType = $this->getRequest()->getParam('entity_type', null);
                $entityId = $this->getRequest()->getParam('entity_id', null);

                if (!$this->formKeyValidator->validate($this->getRequest())
                    || !$this->mwzhelper->isAdminLoggedin()
                    || !$templateId
                ) {
                    throw new \Exception('Could not authenticate your request.');
                }

                // Get email template based on type requested
                list($emailTemplate, $templateCode) = $this->loadEmailTemplate($templateType, $templateId);

                // Load data to pass to email template
                $templateVars = $this->loadTemplateVars($entityType, $entityId, $storeId);

                $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

                try {
                    $emailAddress = ($emailAddress) ? $emailAddress : $this->mwzhelper::SENDTEST_EMAILADDRESS;
                    $transport = $this->mwzhelper->prepareEmail($emailAddress, $templateId, $templateVars, $storeId);
                }catch(\Exception $e) {
                    if(false === stripos($e->getMessage(), 'not found')){
                        throw $e;
                    }
                    $transport = $this->mwzhelper->prepareEmail($emailAddress, $templateId, $templateVars, $storeId, \Magento\Framework\App\Area::AREA_ADMINHTML);
                }
                $message = ZendMessage::fromString($transport->getMessage()->getRawMessage())->setEncoding('utf-8');

                $this->emulation->stopEnvironmentEmulation();

                if($emailAddress !== $this->mwzhelper::SENDTEST_EMAILADDRESS){
                    $transport->sendMessage();
                    $successMessages[] = __('Email sent to %1', $emailAddress);
                }

                $templateProcessed = quoted_printable_decode($message->getBodyText());
                $templateProcessed = $this->maliciousCode->filter($templateProcessed);

                if ($emailTemplate->isPlain()) {
                    $templateProcessed = "<pre>" . $this->escaper->escapeHtml($templateProcessed) . "</pre>";
                }

                \Magento\Framework\Profiler::stop($this->profilerName);

                $response['template_id'] = $templateId;
                $response['template_type'] = $templateType;
                $response['template_code'] = $templateCode;
                $emailHtml = $templateProcessed;
            }

        } catch (\Exception $e) {
            $hasError = true;
            $errorMessages[] = $e->getMessage();
            $this->mwzLogger->error('Mentalworkz_EmailPreview Error: ' . $e->getMessage());
        }

        if(!empty($errorMessages)){
            $response['messages'][] = ['error' => $errorMessages];
        }
        if(!empty($successMessages)){
            $response['messages'][] = ['success' => $successMessages];
        }
        $response['error'] = $hasError;
        $response['html'] = $emailHtml;

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($response);
    }

    /**
     * @param $templateType
     * @param $templateId
     * @return array
     * @throws \Exception
     */
    private function loadEmailTemplate($templateType, $templateId): array
    {
        try{
            $templateCode = null;
            if($templateType === 'custom'){
                /** @var $emailTemplate \Magento\Email\Model\Template */
                $emailTemplate = $this->emailFactory->create();
                $this->templateResource->load($emailTemplate, $templateId);

                $templateCode = $emailTemplate->getTemplateCode();
            }else {
                $templateCode = $templateId;

                /** @var $emailTemplate \Magento\Email\Model\BackendTemplate */
                $emailTemplate = $this->backendTemplate;
                $parts = $this->emailConfig->parseTemplateIdParts($templateId);
                $templateId = $parts['templateId'];
                $theme = $parts['theme'];
                if ($theme) {
                    $emailTemplate->setForcedTheme($templateId, $theme);
                }
                $emailTemplate->setForcedArea($templateId)
                    ->loadDefault($templateId)
                    ->setData('orig_template_code', $templateId)
                    ->setData('template_variables',
                        $this->serializer->serialize($emailTemplate->getVariablesOptionArray(true)));
            }

        }catch(NoSuchEntityException $e) {
            $errMessage = __('Could not load email template of type %1 with ID  %2', $templateType, $templateId);
            throw new \Exception((string) $errMessage);
        }

        return [$emailTemplate, $templateCode];
    }

    /**
     * @param $entityType
     * @param $entityId
     * @return array
     * @throws \Exception
     */
    private function loadTemplateVars (string $entityType, string $entityId, int $storeId): array
    {
        $templateVars = [];
        if ($entityType && $entityId) {
            /** @var $entity \Mentalworkz\EmailPreview\Model\FetchEntity */
            $entity = $this->fetchEntity->getEntity($entityType, $entityId);
            if (!$entity) {
                $errMessage = __('Entity of type %1 with ID %2 not found',$entityId, $entityId);
                throw new \Exception((string) $errMessage);
            }

            /** @var $templateVars  \Mentalworkz\EmailPreview\Model\TemplateData */
            $templateVars = $this->templateData->getTemplateVars($entityType, $entity, $storeId);
        }

        return $templateVars;
    }

}
