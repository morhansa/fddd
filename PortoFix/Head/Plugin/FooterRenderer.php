<?php
namespace PortoFix\Head\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Result\Page;
use Smartwave\Porto\Block\Template;

class FooterRenderer
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
     * إضافة بلوك الفوتر إلى صفحة النتيجة
     *
     * @param Page $subject
     * @param Page $result
     * @return Page
     */
    public function afterRenderResult(Page $subject, $result)
    {
        try {
            // الحصول على layout من صفحة النتيجة
            $layout = $subject->getLayout();
            
            // التحقق من وجود footer_block
            if (!$layout->getBlock('footer_block')) {
                // إنشاء بلوك الفوتر
                $footerBlock = $layout->createBlock(
                    Template::class,
                    'footer_block',
                    ['data' => ['template' => 'Smartwave_Porto::html/footer.phtml']]
                );
                
                // إضافة البلوك إلى حاوية الفوتر
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