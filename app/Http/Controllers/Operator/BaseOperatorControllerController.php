<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseOperatorControllerController extends Controller
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        log_message('debug', 'BaseOperatorController dijalankan');
        if (!session('isLoggedIn')) {
            redirect()->to('/login')->send();
            exit();
        }elseif(session('role') != 'operator'){
            return redirect('/home');
        }
    }
}
