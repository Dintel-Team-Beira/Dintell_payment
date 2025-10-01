<?php
// app/View/Components/SubscriptionPopup.php

namespace App\View\Components;

use Illuminate\View\Component;

class SubscriptionPopup extends Component
{
    public $limits;
    public $warnings;
    public $company;
    public $plan;
    public $forceShow;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $limits = [],
        $warnings = [],
        $company = null,
        $plan = null,
        $forceShow = false
    ) {
        $this->limits = $limits;
        $this->warnings = $warnings;
        $this->company = $company;
        $this->plan = $plan;
        $this->forceShow = $forceShow;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.subscription-popup');
    }
}
?>
