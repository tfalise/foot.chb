<?php

class User extends Controller {

    function __construct() {
        parent::Controller();
    }

    function register() {
        // Load required libraries
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_message('required', 'Le champ "%s" est obligatoire');
        $this->form_validation->set_message('max_length', 'La taille maximum du champ "%s" est de %s caract&egrave;res.');
        $this->form_validation->set_message('valid_email', 'Le champ "%s" doit contenir une adresse email valide.');
        
        // Validate form input
        $this->form_validation->set_rules('userName', 'Nom', 'trim|required|xss_clean|max_length[64]|html_entities');
        $this->form_validation->set_rules('userSurname', 'Prénom', 'trim|required|xss_clean|max_length[64]|html_entities');
        $this->form_validation->set_rules('userMail', 'Adresse mail', 'trim|required|valid_email|max_length[256]');
        $this->form_validation->set_rules('userPwd', 'Mot de passe', 'trim|required|matches[userPwdConfirm]');
        $this->form_validation->set_rules('userPwdConfirm', 'Confirmation du mot de passe', 'trim|required');
        
        $this->load->view('header');
        $this->load->view('menu');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->load->view('user_create');
        }
        else
        {
            // Load required model
            $this->load->model('Usermodel', 'user');
        
            // Preparing user data
            $userData = array (
                'email'     => $this->input->post('userMail'),
                'name'      => $this->input->post('userName'),
                'surname'   => $this->input->post('userSurname'),
                'pwd'       => $this->input->post('userPwd')
                );
                
            // Save user data
            $this->user->createUser($userData);
        
            // Render success page
            $this->load->view('user_create_success');
        }
        
        $this->load->view('footer');
    }
    
    function validateKey($userId, $key) {
        // Chargement lib nécessaire
        $this->load->model('Usermodel', 'user');
        
        // Validation user
        $validated = $this->user->validateUser($userId, $key);
        
        $this->load->view('header');
        $this->load->view('menu');
        
        if($validated)
        {
            $this->load->view('user_validate_success');
        }
        else
        {
            $this->load->view('user_validate_error');
        }
        
        $this->load->view('footer');
    }
    
    function login() {       
        $this->load->library('form_validation');
    
        $this->form_validation->set_message('required', 'Le champ "%s" est obligatoire');
        $this->form_validation->set_message('valid_email', 'Le champ "%s" doit contenir une adresse email valide.');       
        
        $this->form_validation->set_rules('userMail', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('userPwd', 'Heure de d&eacute;but', 'trim|required');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('user_login');
            $this->load->view('footer');
        }
        else
        {
            $login = $this->input->post('userMail');
            $pwd = $this->input->post('userPwd');
        
            $this->load->model('Usermodel', 'users');
            $authResult = $this->users->authenticateUser($login, $pwd);
            
            if(!$authResult)
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('user_login');
                $this->load->view('footer');
            }
            else
            {
                $this->session->set_userdata('user', $authResult);
                redirect(site_url(''));
            }
        }
    }
    
    function logout() {
        $sessionUser = $this->session->userdata('user');
        
        if($sessionUser != false)
        {
            $this->session->unset_userdata('user');
        }
        
        redirect('');
    }
 
	function all() {
		$sessionUser = $this->session->userdata('user');
		
		// Check that a user is logged and can admin users
		if(!$sessionUser || !$sessionUser['canAdminUser'])
		{
			redirect('');
		}
		
		$this->load->model('Usermodel', 'users');
		$users = $this->users->getAllUsers();
		
		$viewData = array (
			'users'	=> $users
			);
		
		$this->load->view('header');
        $this->load->view('menu');
        $this->load->view('user_list', $viewData);
        $this->load->view('footer');
	}
	
	function edit($userId) {
		$sessionUser = $this->session->userdata('user');
		
		// Check that a user is logged and can admin users
		if(!$sessionUser || !$sessionUser['canAdminUser'])
		{
			redirect('');
		}
		
		$this->load->helper('form');
		
		// Retrieve edited user data
		$this->load->model('Usermodel', 'users');
		$user = $this->users->getUser($userId);
		
		$viewData = array (
			'user'	=> $user
			);
			
		$this->load->view('header');
        $this->load->view('menu');
        $this->load->view('user_edit', $viewData);
        $this->load->view('footer');
	}
	
	function save($userId) {
		$sessionUser = $this->session->userdata('user');
		
		// Check that a user is logged and can admin users
		if(!$sessionUser || !$sessionUser['canAdminUser'])
		{
			redirect('');
		}
		
		// Retrieve edited user data
		$this->load->model('Usermodel', 'users');
		
		$userRights = array(
			'canSubscribe'		=> $this->input->post('canSubscribe'),
			'canCreateEvents'	=> $this->input->post('canCreateEvents'),
			'canAdminUsers'		=> $this->input->post('canAdminUsers')
			);
			
		$this->users->updateUserRights($userId, $userRights);
		
		$this->load->view('header');
        $this->load->view('menu');
        $this->load->view('user_edit_success');
        $this->load->view('footer');
	}
}

?>