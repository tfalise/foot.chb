<?php

    class Usermodel extends Model {
        function __construct() {
            parent::Model();
        }
        
        function createUser($userData) {
            // Encrypt user password
            $userData['pwd'] = $this->_encryptPassword($userData['pwd']);
        
            // Insert user
            $this->db->insert('foot_users', $userData);
            
            $userId = $this->db->insert_id();
            
            // Generate user validation key
            $validationData = array(
                'idUser'    => $userId,
                'key'       => $this->_generateKey()
                );
            
            // Save key
            $this->db->insert('foot_user_keys', $validationData);
            
            // Send user validation email
            mail(
                $userData['email'], 
                'Validation compte foot.chb', 
                'Pour valider votre compte, merci de suivre <a href="http://foot.chb.free.fr/index.php/user/validateKey/' . $userId . '/' . $validationData['key'] . '">ce lien</a>.');
            
        }
        
        function validateUser($idUser, $userKey) {
        
            // Create criteria array
            $keyCriterias = array(
                'idUser'    => $idUser,
                'key'       => $userKey
                );
        
            // Retrieve key
            $this->db->from('foot_user_keys')->where($keyCriterias);
            $keyCount = $this->db->count_all_results();
            
            // If a key exists, update user and delete key
            if($keyCount > 0)
            {
                $updateData = array (
                    'isActive'  => true
                    );
                    
                $this->db->where('id', $idUser);
                $this->db->update('foot_users', $updateData);
                
                $this->db->where($keyCriterias);
                $this->db->delete('foot_user_keys');
                
                return true;
            }
            
            return false;
        }
        
        function updateUser($userData) {
        }
        
        function authenticateUser($email, $password) {
            $encPassword = $this->_encryptPassword($password);
            
            $criterias = array(
                'email'     => $email,
                'pwd'       => $encPassword
                );
                
            $this->db->where($criterias);
            $query = $this->db->get('foot_users');
            
            if($query->num_rows() == 1)
            {
                return $query->row_array();
            }
            else
            {
                return false;
            }
        }
        
        function _encryptPassword($password) {
            return sha1($password . $this->config->item('encryption_key'));
        }
        
        function _generateKey() {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            
            // Length of character list
            $chars_length = (strlen($chars) - 1);

            // Start our string
            $string = $chars{rand(0, $chars_length)};
           
            // Generate random string
            for ($i = 1; $i < 64; $i = strlen($string))
            {
                // Grab a random character from our list
                $r = $chars{rand(0, $chars_length)};
               
                // Make sure the same two characters don't appear next to each other
                if ($r != $string{$i - 1}) $string .=  $r;
            }
           
            // Return the string
            return $string;
        }
    }
    
?>