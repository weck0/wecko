<?php
/**
 * Created by PhpStorm.
 * User: Florian Ceprika
 * Date: 12/08/2016
 * Time: 12:30
 */

class Wecko_Question_Adminhtml_QuestionController extends Mage_Adminhtml_Controller_Action{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('question/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }
    
    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('question/adminhtml_question'));
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $questionId = $this->getRequest()->getParam('id');
        $questionModel = Mage::getModel('question/question')->load($questionId);
        
        if ($questionModel->getId() || $questionId == 0) {
            
            Mage::register('question_data', $questionModel);
            $this->loadLayout();
            $this->_setActiveMenu('question/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('question/adminhtml_question_edit'))
            ->_addLeft($this->getLayout()->createBlock('question/adminhtml_question_edit_tabs'));
            $this->renderLayout();
            
        } else {
            
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('question')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
    if ( $this->getRequest()->getPost() ) {
        try {
            $postData = $this->getRequest()->getPost();
            Mage::getModel('question/question')->saveForm($postData);

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setquestionData(false);
            $this->_redirect('*/*/');
            return;

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setquestionData($this->getRequest()->getPost());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
        }
    }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $questionModel = Mage::getModel('question/question');
                $questionModel->setId($this->getRequest()->getParam('id'))
                            ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');

                } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/');
    }

    /**
    * Product grid for AJAX request.
    * Sort and filter result for example.
    */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('importedit/adminhtml_question_grid')->toHtml());
    }
    
}