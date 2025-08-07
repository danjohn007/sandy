<?php
/**
 * Home Controller
 */

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        $this->view('home/index', [
            'title' => 'Sandy Beauty Nails - Reserva tu Cita'
        ]);
    }
}