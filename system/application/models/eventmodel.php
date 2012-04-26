<?php

    class Eventmodel extends Model {
        function __construct() {
            parent::Model();
        }
        
        function getEventList($from, $to) {
            $this->db->select("id, DATE(startDate) as eventDate, capacity, COUNT(foot_subscriptions.idUser) as subscribed", FALSE);
            $this->db->from('foot_events');
            $this->db->join('foot_subscriptions','foot_events.id = foot_subscriptions.idEvent', 'left');
            $this->db->where("startDate >= '" . date('Y-m-d', $from) . "' AND startDate < '" . date('Y-m-d', $to) . "'");
            $this->db->group_by('id');
            $query = $this->db->get();
            
            return $query->result_array();
        }
        
        function getEvent($eventId) {
            $criterias = array(
                'id'   => $eventId
                );
                
            $this->db->where($criterias);
            $query = $this->db->get('foot_events');
            
            return $query->row_array();
        }
        
        function createEvent($eventData) {
            // Fee management
            if(!isset($eventData['fee']))
            {
                $eventData['fee'] = 0;
            }
        
            // Insert event
            $this->db->insert('foot_events', $eventData);
            
            $eventId = $this->db->insert_id();
            
            // Add owner subscription
            $this->subscribeToEvent($eventId, $eventData['idOwner']);
        }
        
		function deleteEvent($eventId) {
			// Clear event subscriptions
			$this->clearEventSubscriptions($eventId);
		
			$criterias = array(
				'id'	=> $eventId
				);
				
			$this->db->where($criterias);
			$this->db->delete('foot_events');
		}
		
        function subscribeToEvent($eventId, $userId) {
            // Add subscription for user
            $subscriptionData = array(
                'idEvent'       => $eventId,
                'idUser'        => $userId,
                'date'          => date('Y-m-d H-i-s')
                );
            
            $this->db->insert('foot_subscriptions', $subscriptionData);
        }
        
        function unsubscribeToEvent($eventId, $userId) {
            $criterias = array(
                'idEvent'   => $eventId,
                'idUser'    => $userId
                );
                
            $this->db->where($criterias);
            $this->db->delete('foot_subscriptions');
        }
        
		function clearEventSubscriptions($eventId) {
			$criterias = array(
				'idEvent'	=> $eventId
				);
				
			$this->db->where($criterias);
			$this->db->delete('foot_subscriptions');
		}
		
        function getEventSubscriptions($eventId) {        
            $this->db->select("foot_subscriptions.idUser, CONCAT(foot_users.surname ,' ' ,foot_users.name) as userName", FALSE);
            $this->db->from('foot_subscriptions');
            $this->db->join('foot_users', 'foot_subscriptions.idUser = foot_users.id');
            $this->db->where('foot_subscriptions.idEvent', $eventId);
            $this->db->order_by('foot_subscriptions.date','asc');
            $query = $this->db->get();
            
            return $query->result_array();
        }
    }

?>