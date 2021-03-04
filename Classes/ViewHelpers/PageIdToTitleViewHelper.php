<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * PageIdToTitleViewHelper
 */
class PageIdToTitleViewHelper extends AbstractViewHelper
{
    /**
     * Return page title
     *
     * @param int $pageId
     *
     * @return string
     */
    public function render($pageId)
    {
        if (!is_numeric($pageId)) {
            return $pageTitle = '';
        }

        /** @var PageRepository $pageRepository */
        $pageRepository = $this->objectManager->get(PageRepository::class);
        $pageTitle = $pageRepository->getPage($pageId)['title'];

        return $pageTitle;
    }
}
