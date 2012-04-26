<?php

class Event extends Controller {

    function __construct() {
        // Base constructor
        parent::Controller();
    }

    function index() {
        $this->load->view('header');
        $this->load->view('menu');
        
        // Event model
        $this->load->model('Eventmodel', 'events');
        
        // Retrieve events for month + 2 next months
        $now = getdate();
        $startDate = $this->_getStartDate($now);
        $endDate = $this->_getEndDate($now);
        
        // Prepare events
        $eventList = $this->_prepareEventList($startDate, $endDate);
        
        // Set calendar parameters
        $prefs = array(
            'template'  => $this->_getCalendarTemplate(),
            'start_day' => 'monday'
            );
        $this->load->library('calendar', $prefs);
        
        // Build calendar html code
        $calendarHtml = '';
        
        for($i = 0; $i < 4; $i++)
        {
            $realMonth = ($now['mon'] + $i) > 12 ? (($now['mon'] + $i) - 12) : ($now['mon'] + $i);
            $realYear = ($now['mon'] + $i) > 12 ? $now['year'] + 1 : $now['year'];
        
            $calendarHtml .= $this->calendar->generate(
                $realYear,
                $realMonth,
                $eventList[$realMonth]
                );
        }
        
        // Display calendar view
        $viewData = array(
            'calendar_html' => $calendarHtml
            );
            
        $this->load->view('event_calendar', $viewData);
        $this->load->view('footer');
    }
    
    function create($year, $month, $day) {
        $sessionUser = $this->session->userdata('user');
        
        if(!$sessionUser || !$sessionUser['canCreateEvents'])
        {
            redirect('');
        }
    
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->load->view('header');
        $this->load->view('menu');
        
        $this->form_validation->set_message('required', 'Le champ "%s" est obligatoire');
        $this->form_validation->set_message('max_length', 'La taille maximum du champ "%s" est de %s caract&egrave;res.');
        $this->form_validation->set_message('is_natural_no_zero', 'Le champ "%s" doit &ecirc;tre un entier strictement positif.');
        
        $this->form_validation->set_rules('eventName', 'Nom', 'trim|required|xss_clean|max_length[64]|html_entities');
        $this->form_validation->set_rules('eventStartDate', 'Heure de d&eacute;but', 'callback__check_DateFormat');
        $this->form_validation->set_rules('eventEndDate', 'Heure de fin', 'callback__check_DateFormat');
        $this->form_validation->set_rules('eventCapacity', 'Places', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('eventFee', 'Prix / Personne', 'trim|numeric');
        
        $eventDate = mktime(0, 0, 0, $month, $day, $year);
        
        if($this->form_validation->run() == FALSE)
        {
            $viewData = array (
                'eventDate' => $eventDate
                );
        
            $this->load->view('event_create', $viewData);
        }
        else
        {
            // Prepare event data
            $eventData = array(
                'idOwner'   => $sessionUser['id'],       // SET SESSION USER
                'name'      => $this->input->post('eventName'),
                'startDate' => $year . '-' . $month . '-' . $day . ' ' . $this->input->post('eventStartDate'),
                'endDate'   => $year . '-' . $month . '-' . $day . ' ' . $this->input->post('eventEndDate'),
                'capacity'  => $this->input->post('eventCapacity'),
                'fee'       => $this->input->post('eventFee')
                );
                
            $this->load->model('Eventmodel', 'events');
            $this->events->createEvent($eventData);
        
            $this->load->view('event_create_success');
        }
        
        $this->load->view('footer');
    }
    
    function view($eventId) {
        $sessionUser = $this->session->userdata('user');
    
        $this->load->view('header');
        $this->load->view('menu');
        
        $this->load->model('Eventmodel','events');
        
        $event = $this->events->getEvent($eventId);
        $subscriptions = $this->events->getEventSubscriptions($eventId);
        
        $viewData = array(
            'user'  => $sessionUser,
            'event' => $event,
            'subscriptions' => $subscriptions
            );
            
        $this->load->view('event_view', $viewData);
        
        $this->load->view('footer');
    }

	function delete($eventId) {
		$sessionUser = $this->session->userdata('user');
		
		if(!$sessionUser || !$sessionUser['canCreateEvents'])
		{
			redirect('');
		}
        
        $this->load->model('Eventmodel','events');
		$event = $this->events->getEvent($eventId);
		
		if($sessionUser['id'] != $event['idOwner'])
		{
			redirect('');
		}
		
		$this->events->deleteEvent($eventId);
		
		redirect('');
	}
	
    function subscribe($eventId) {  
        $sessionUser = $this->session->userdata('user');
        
        if(!$sessionUser || !$sessionUser['canSubscribe'])
        {
            redirect('');
        }
    
        $this->load->model('Eventmodel', 'events');
        $this->events->subscribeToEvent($eventId, $sessionUser['id']);
        
        redirect(site_url('event/view/' . $eventId));
    }
    
    function unsubscribe($eventId) {        
        $sessionUser = $this->session->userdata('user');
        
        if(!$sessionUser)
        {
            redirect('');
        }
    
        $this->load->model('Eventmodel', 'events');
        $this->events->unsubscribeToEvent($eventId, $sessionUser['id']);
        
        redirect(site_url('event/view/' . $eventId));
    }

    function _check_DateFormat($str) {
        $datePattern = '/^\d{2}:\d{2}$/';
    
        if(preg_match($datePattern, $str) < 1)
        {    
            $this->form_validation->set_message('_check_DateFormat', 'La date du champ %s n\'est pas dans le bon format');
            return false;
        }
        
        return true;
    }
    
    function _getStartDate($refDate) {
        return mktime(0, 0, 0, $refDate['mon'], $refDate['mday'], $refDate['year']);
    }
    
    function _getEndDate($refDate) {        
        switch($refDate['month'])
        {
            case 9:
            case 10:
            case 11:
            case 12:
                return mktime(0, 0, 0, ($refDate['mon'] + 4) % 12, 1, $refDate['year'] + 1);
            default:
                return mktime(0, 0, 0, $refDate['mon'] + 4, 1, $refDate['year']);
        }
    }
    
    function _prepareEventList($startDate, $endDate) {
        // Retrieve existing events from DB
        $eventList = $this->events->getEventList($startDate, $endDate);
   
        $events = array();
        
        while($startDate < $endDate)
        {
            if(!isset($events[date('n', $startDate)]))
            {
                $events[date('n', $startDate)] = array();
            }
        
            $event = $this->_findEvent($eventList, $startDate);
        
            if($event == false)
            {
                $events[date('n', $startDate)][date('j', $startDate)] = anchor('event/create/' . date('Y/n/j', $startDate), date('j', $startDate), array('class' => 'noEvent'));
            }
            else
            {
                if($event['subscribed'] >= $event['capacity'])
                {
                    $events[date('n', $startDate)][date('j', $startDate)] = anchor('event/view/' . $event['id'], date('j', $startDate), array('class' => 'fullEventDay'));
                }
                else
                {
                    $events[date('n', $startDate)][date('j', $startDate)] = anchor('event/view/' . $event['id'], date('j', $startDate), array('class' => 'openEventDay'));
                }
            }

            // Add one day
            $startDate += 86400;
        }
        
        return $events;
    }
    
    function _findEvent($eventList, $eventDate) {   
        foreach($eventList as $event)
        {
            if($event['eventDate'] == date('Y-m-d', $eventDate))
            {
                return $event;
            }
        }
        
        return false;
    }
    
    function _getCalendarTemplate() {
        return '
           {table_open}<div class="calendarBox"><table class="calendar">{/table_open}

           {heading_row_start}<tr>{/heading_row_start}

           {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
           {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
           {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

           {heading_row_end}</tr>{/heading_row_end}

           {week_row_start}<tr>{/week_row_start}
           {week_day_cell}<td class="weekDay">{week_day}</td>{/week_day_cell}
           {week_row_end}</tr>{/week_row_end}

           {cal_row_start}<tr>{/cal_row_start}
           {cal_cell_start}{/cal_cell_start}

           {cal_cell_content}<td>{content}</td>{/cal_cell_content}
           {cal_cell_content_today}<td>{content}</td>{/cal_cell_content_today}

           {cal_cell_no_content}<td class="pastDay">{day}</td>{/cal_cell_no_content}
           {cal_cell_no_content_today}<td class="pastDay">{day}</td>{/cal_cell_no_content_today}

           {cal_cell_blank}<td class="noDay">&nbsp;</td>{/cal_cell_blank}

           {cal_cell_end}{/cal_cell_end}
           {cal_row_end}</tr>{/cal_row_end}

           {table_close}</table></div>{/table_close}
           ';
    }
    
    function _addDate($givendate,$day=0,$mth=0,$yr=0) {
        $cd = strtotime($givendate);
        $newdate = date('Y-m-d h:i:s', mktime(date('h',$cd),
            date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
            date('d',$cd)+$day, date('Y',$cd)+$yr));
        return $newdate;
    }
}

?>