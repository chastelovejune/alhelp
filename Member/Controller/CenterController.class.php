<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class CenterController extends MemberBaseController {
    public function index() {
        $this->redirect('/member/MemberPost');
    }
}
