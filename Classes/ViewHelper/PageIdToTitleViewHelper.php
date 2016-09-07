<?php
namespace JWeiland\RecommendAPage\ViewHelper;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * PageIdToTitleViewHelper
 */
class PageIdToTitleViewHelper extends AbstractViewHelper
{
    /**
     * @param int $pageId
     *
     * Return page title
     */
    public function render($pageId)
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = $this->objectManager->get(PageRepository::class);
        return $pageRepository->getPage($pageId)['title'];
    }
}