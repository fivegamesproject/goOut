<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewVerifiedTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden() && $this->review->is_verified) {
            $icon = '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20"><path fill="currentColor" d="M10 2a8 8 0 1 1 0 16 8 8 0 1 1 0-16zm3.855 3.34l-5.14 5.93-2.57-2.4-1.34 1.25 3.24 4.54h1.34l5.81-8.38-1.34-.94z"/></svg>';
            $text = esc_attr__('Verified', 'site-reviews');
            $value = sprintf('%s <span>%s</span>', $icon, $text);
            return $this->wrap($value);
        }
    }
}
