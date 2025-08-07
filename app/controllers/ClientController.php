<?php
/**
 * Client Controller (Admin)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Client.php';

class ClientController extends BaseController {
    private $clientModel;
    
    public function __construct() {
        parent::__construct();
        $this->clientModel = new Client();
    }
    
    public function index() {
        $this->requireAuth();
        
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 20;
        
        if (!empty($search)) {
            $clients = $this->clientModel->searchClients($search);
            if (!is_array($clients)) {
                $clients = [];
            }
            $pagination = [
                'data' => $clients,
                'total' => count($clients),
                'page' => 1,
                'perPage' => count($clients),
                'totalPages' => 1
            ];
        } else {
            $pagination = $this->clientModel->paginate($page, $perPage, [], 'name ASC');
            if (!is_array($pagination)) {
                $pagination = [
                    'data' => [],
                    'total' => 0,
                    'page' => 1,
                    'perPage' => $perPage,
                    'totalPages' => 1
                ];
            }
        }
        
        // Get frequent clients with safe default
        $frequentClients = $this->clientModel->getFrequentClients(10);
        if (!is_array($frequentClients)) {
            $frequentClients = [];
        }
        
        $this->view('admin/clients/index', [
            'title' => 'Clientes - Administración',
            'clients' => $pagination,
            'frequentClients' => $frequentClients,
            'search' => $search
        ]);
    }
    
    public function show() {
        $this->requireAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/clients');
            return;
        }
        
        $client = $this->clientModel->find($id);
        if (!$client) {
            $this->redirect('/admin/clients');
            return;
        }
        
        // Get appointment history
        $appointmentHistory = $this->clientModel->getAppointmentHistory($id, 50);
        
        $this->view('admin/clients/show', [
            'title' => 'Cliente: ' . $client['name'] . ' - Administración',
            'client' => $client,
            'appointmentHistory' => $appointmentHistory
        ]);
    }
}