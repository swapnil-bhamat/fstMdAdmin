<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {
    public function __construct()
    {
                parent::__construct();
                // Your own constructor code
                $this->load->database();
                $this->load->helper('login_helper');
                $this->load->helper('sms_helper');
    }
    function signout(){
        $this->session->sess_destroy();
        redirect("admin");
    }
	public function index()
	{
		if(_is_user_login($this)){
		    $url = _get_user_redirect($this);
            redirect($url);
        }else{
            
            $data = array("error"=>"");       
            if(isset($_POST))
            {
                
                $this->load->library('form_validation');
                
                $this->form_validation->set_rules('email', 'Email', 'trim|required');
                $this->form_validation->set_rules('password', 'Password', 'trim|required');
                if ($this->form_validation->run() == FALSE) 
        		{
        		   if($this->form_validation->error_string()!=""){
        			$data["error"] = '<div class="alert alert-warning alert-dismissible" role="alert">
                                  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                  <strong>Warning!</strong> '.$this->form_validation->error_string().'
                                </div>';
                    }
        		}else
                {
                   
                    $q = $this->db->query("Select * from `users` where (`user_email`='".$this->input->post("email")."') and user_password='".md5($this->input->post("password"))."'  Limit 1");
                    
                   // print_r($q) ; 
                    if ($q->num_rows() > 0)
                    {
                        $row = $q->row(); 
                        if($row->user_status == "0")
                        {
                            $data["error"] = '<div class="alert alert-danger alert-dismissible" role="alert">
                                  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                  <strong>Warning!</strong> Your account currently inactive.</div>';
                        }
                        else
                        {
                            $newdata = array(
                                                   'user_name'  => $row->user_fullname,
                                                   'user_email'     => $row->user_email,
                                                   'logged_in' => TRUE,
                                                   'user_id'=>$row->user_id,
                                                   'user_type_id'=>$row->user_type_id
                                                  );
                            $this->session->set_userdata($newdata);
                            redirect(_get_user_redirect($this));
                         
                        }
                    }
                    else
                    {
                        $data["error"] = '<div class="alert alert-danger alert-dismissible" role="alert">
                                  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                  <strong>Warning!</strong> Invalid User and password. </div>';
                    }
                   
                    
                }
            }
            $data["active"] = "login";
            
            $this->load->view("admin/login",$data);
        }
	}
    public function dashboard(){
         if(_is_user_login($this)){
            $data = array();
            $this->load->model("order_model");
              $date = date("Y-m-d");
            $data["today_orders"]  = $this->order_model->get_today_order($date);
             $nexday = date('Y-m-d', strtotime(' +1 day'));
            $data["next_day_orders"]  = $this->order_model->get_today_order($nexday);
            $this->load->view("admin/dashboard",$data);   
            
           
        } 
        else
        {
            redirect('admin');
        }
 
       
    } 
   
}
