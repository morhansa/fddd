<?php
namespace PortoFix\Head\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Result\Page;

class HeaderRenderer
{
    /**
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * @var LayoutInterface
     */
    protected $layout;
    
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @param RequestInterface $request
     * @param LayoutInterface $layout
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RequestInterface $request,
        LayoutInterface $layout,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->layout = $layout;
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * حقن محتوى الهيدر في DOM
     *
     * @param Page $subject
     * @param Page $result
     * @return Page
     */
    public function afterRenderResult(Page $subject, $result)
    {
        try {
            // إنشاء بلوك الهيدر
            $layout = $subject->getLayout();
            if (!$layout->getBlock('porto_header')) {
                $headerBlock = $layout->createBlock(
                    \Smartwave\Porto\Block\Template::class,
                    'porto_header',
                    ['data' => ['template' => 'Smartwave_Porto::html/header.phtml']]
                );
                
                // استهداف المكان الذي نريد إضافة الهيدر فيه
                $pageWrapperContainer = $layout->getBlock('page.wrapper');
                if ($pageWrapperContainer) {
                    $pageWrapperContainer->setChild('porto_header', $headerBlock);
                }
                
                // أيضًا نضيف الفوتر بنفس الطريقة
                $footerBlock = $layout->createBlock(
                    \Smartwave\Porto\Block\Template::class,
                    'footer_block',
                    ['data' => ['template' => 'Smartwave_Porto::html/footer.phtml']]
                );
                
                $footerContainer = $layout->getBlock('footer-container');
                if ($footerContainer) {
                    $footerContainer->setChild('footer_block', $footerBlock);
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            // في حالة حدوث خطأ، عرض الصفحة دون تغيير
            return $result;
        }
    }
}