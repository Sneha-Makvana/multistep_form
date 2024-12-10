<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        return view('user_form');
    }

    public function validateStep()
    {
        $response = ['status' => false, 'errors' => []];
        $validation = \Config\Services::validation();
        $step = $this->request->getPost('current_step');

        if ($step == 1) {
            $validation->setRules([
                'full_name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email',

            ]);
        } elseif ($step == 2) {
            $validation->setRules([
                'gender' => 'required',
                'interests' => 'required',
            ]);
        } elseif ($step == 3) {
            $validation->setRules([
                'country' => 'required',
                'resume' => 'uploaded[resume]|max_size[resume,10240]|ext_in[resume,txt,pdf,doc,docx]',
            ]);
        }

        if (!$validation->withRequest($this->request)->run()) {
            $response['errors'] = $validation->getErrors();
        } else {
            $response['status'] = true;
        }

        return $this->response->setJSON($response);
    }

    public function insertData()
    {
        $response = ['status' => false, 'message' => 'Form submission failed!'];

        $userModel = new UserModel();
        $validation = \Config\Services::validation();

        $validation->setRules([
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email',
            'gender' => 'required',
            'country' => 'required',
            'resume' => 'uploaded[resume]|max_size[resume,10240]|ext_in[resume,pdf,txt,doc,docx]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $response['errors'] = $validation->getErrors();
        } else {
            $data = $this->request->getPost();

            if (isset($data['interests']) && is_array($data['interests'])) {
                $data['interests'] = implode(',', $data['interests']);
            }

            if ($file = $this->request->getFile('resume')) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads', $newName);
                    $data['resume'] = $newName;
                }
            }

            if ($userModel->insert($data)) {
                $response = [
                    'status' => true,
                    'message' => 'Form submitted successfully!',
                ];
            }
        }

        return $this->response->setJSON($response);
    }

    public function displayData()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->get_users(5, 0);
        $data['total_users'] = $userModel->get_total_users();
        return view('display_users', $data);
    }
    public function fetchPaginatedData()
    {
        $userModel = new UserModel();

        $limit = $this->request->getVar('limit') ?? 5;
        $offset = $this->request->getVar('offset') ?? 0;
        $search = $this->request->getVar('search');
        $gender = $this->request->getVar('gender');

        $query = $userModel;

        if (!empty($search)) {
            $query = $query->like('full_name', $search)
                ->orLike('email', $search);
        }

        if (!empty($gender)) {
            $query = $query->where('gender', $gender);
        }

        $data = $query->orderBy('id', 'ASC')->findAll($limit, $offset);
        $total_rows = $query->countAllResults(false);

        return $this->response->setJSON([
            'status' => true,
            'data' => $data,
            'total_rows' => $total_rows,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }


    public function deleteUser($id)
    {
        $userModel = new UserModel();
        if ($userModel->delete($id)) {
            return $this->response->setJSON(['status' => true, 'message' => 'User deleted successfully.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Failed to delete user.']);
    }

    public function getUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->where('id', $id)->first();
        if ($user) {
            return json_encode(['status' => true, 'user' => $user]);
        } else {
            return json_encode(['status' => false, 'message' => 'User not found']);
        }
    }

    public function updateUser()
    {
        $data = $this->request->getPost();
        $response = ['status' => false, 'message' => 'Update failed!'];
        $userModel = new UserModel();

        $existingUser = $userModel->find($data['id']);
        if (!$existingUser) {
            return $this->response->setJSON(['status' => false, 'message' => 'User not found.']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id' => 'required|numeric',
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email',
            'gender' => 'required',
            'country' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $response['errors'] = $validation->getErrors();
        } else {


            if (isset($data['interests']) && is_array($data['interests'])) {
                $data['interests'] = implode(',', $data['interests']);
            }

            if ($userModel->update($data['id'], $data)) {
                $response = [
                    'status' => true,
                    'message' => 'User updated successfully',
                ];
            }
        }

        return $this->response->setJSON($response);
    }
}
