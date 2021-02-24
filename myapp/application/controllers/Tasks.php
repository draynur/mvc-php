<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->form_validation->set_rules('task_name', 'Task Name', 'required|trim');
        $this->form_validation->set_rules('task_body', 'Task Body', 'required|trim');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required');
    }

    public function display($task_id)
    {
        $data['project_id'] = $this->task_model->get_task_project_id($task_id);
        $data['project_name'] = $this->task_model->get_project_name($data['project_id']);

        $data['task'] = $this->task_model->get_task($task_id);
        $data['main_view'] = "tasks/display";
        $this->load->view('layouts/main', $data);
    }

    public function index()
    {
        $project_id = $this->session->userdata('user_id');
        $data['tasks'] = $this->task_model->get_all_tasks($project_id);
        $data['main_view'] = 'tasks/create_task';
        $this->load->view('layouts/main', $data);
    }

    public function create()
    {

        if ($this->form_validation->run()) {

            $data = array(
                'project_id' => $this->uri->segment(3),
                'task_name' => $this->input->post('task_name'),
                'task_body' => $this->input->post('task_body'),
                'due_date' => $this->input->post('due_date'),
                'date_created' => date("Y-m-d h:i:s")
            );

            if ($this->task_model->create_task($data)) {
                $this->session->set_flashdata('task_updated', 'Your task has been created.');

                redirect('projects/display/' . $this->uri->segment(3));
            }
        } else { // Error w/validation
            $data['main_view'] = 'tasks/create_task';
            $this->load->view('layouts/main', $data);
        }
    }

    public function edit($project_id, $task_id)
    {
        if ($this->input->post()) {
            if ($this->form_validation->run()) {

                $data = array(
                    'project_id' => $project_id,
                    'task_name' => $this->input->post('task_name'),
                    'task_body' => $this->input->post('task_body'),
                    'due_date' => $this->input->post('due_date'),
                    'date_created' => date("Y-m-d h:i:s")
                );

                if ($this->task_model->update_task($task_id, $data)) {
                    $this->session->set_flashdata('task_updated', 'Your task has been updated.');
                    // Only redirect if successful
                    redirect('projects/display/' . $project_id);
                }
            }
        }
        // Neat hack, since form validation is automatically ran above
        // It will fail and highlight errors using a function already built into the page
        // No need for extra else brackets!
        $data['project_id'] = $this->task_model->get_task_project_id($task_id);
        $data['the_task'] = $this->task_model->get_task_project_data($task_id);
        $data["main_view"] = "tasks/edit_task";
        $this->load->view('layouts/main', $data);
    }

    public function delete($project_id, $task_id)
    {
        $this->task_model->delete_task($task_id);
        $this->session->set_flashdata('task_updated', 'Your task has been deleted.');
        redirect('projects/display/' . $project_id);
    }
}
