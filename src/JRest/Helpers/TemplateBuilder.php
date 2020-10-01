<?php

namespace JRest\Helpers;

use App\Model\Customer;
use App\Model\MessageTmpl;
use App\Model\Tour;
use App\JFk\Notification\EmailMessage;
use App\JFk\Notification\PushMessage;
use JRest\Models\Notification_Tmpl;

class TemplateBuilder
{


    var $template;
    var $order;
    var $recipient;
    var $agent;
    var $message;
    var $type;
    var $item;

    public function __construct($code, $recipient, $item_id)
    {
        // $this->order = OrderHelper::getDetail($order_id);
        $tmpl = Notification_Tmpl::where('code', $code)
            ->first();
        $this->template = $tmpl;
        $this->recipient = $recipient;
        $this->item_id = $item_id;
        if ($tmpl->ob_type == 'order') {
            $order = []; // get order from db
            $this->item = $order;
        }
    }


    public function getBodyEmail()
    {
        return $this->template->title;
    }

    public function getContentPush()
    {
        return $this->template->push_customer;
    }

    /**
     *
     * @param String $input
     * @param Customer $customer
     * @return mixed
     */
    private function fillCustomer($input)
    {
        // var_dump($this->customer);
        $input = str_replace('{email}', $this->customer->email, $input);
        $input = str_replace('{firstname}', $this->customer->firstname, $input);
        $input = str_replace('{lastname}', $this->customer->lastname, $input);
        $input = str_replace('{image}', $this->customer->image, $input);

        $input = str_replace('{customer}', $this->customer->firstname . ' ' . $this->customer->lastname, $input);


        $input = str_replace('{mobile}', $this->customer->mobile, $input);
        $input = str_replace('{address}', $this->customer->address, $input);
        $input = str_replace('{city}', $this->customer->city, $input);
        $input = str_replace('{mobile}', $this->customer->mobile, $input);
        $input = str_replace('{zip}', $this->customer->zip ? $this->customer->zip : 'N/A', $input);
        $input = str_replace('{country}', $this->customer->country_name, $input);
        $input = str_replace('{company}', $this->customer->company, $input);
        // $input = str_replace('{username}', $this->customer->username, $input);
        return $input;
    }



    private function fillOrder($input)
    {
        $input = str_replace('{order_number}', $this->order->order_number, $input);
        // $input = str_replace('{total}', CurrencyHelper::formatprice($this->order->total), $input);
        $input = str_replace('{notes}', $this->order->notes, $input);
        $input = str_replace('{payment_status}', ($this->order->pay_status), $input);
        // $input = str_replace('{deposit}', CurrencyHelper::formatprice($this->order->deposit), $input);
        $input = str_replace('{depart_city}', $this->order->departure_txt, $input);
        $input = str_replace('{fee}', $this->order->service_fee, $input);
        $input = str_replace('{tax}', $this->order->tax, $input);
        $input = str_replace('{message_cancel}', $this->order->cancel_note, $input);
        $input = str_replace('{pay_method}', $this->order->pay_method, $input);
        // $input = str_replace('{created}', DateHelper::toShort($this->order->created), $input);
        // $input = str_replace('{order_status}', DateHelper::toShort($this->order->order_status), $input);


        //$input = str_replace('{order_link}', $order_link , $input);



        //$order_link_visa = JURI::root() . 'index.php?option=com_bookpro&view=pos&layout=visa';
        //$input = str_replace('{order_link_visa}', $order_link_visa , $input);


        // $link_logo = APP_PATH . 'images/template/logo.png';

        //$input = str_replace('{link_logo}',  $link_logo , $input);


        if ($this->order->airline_class == 'E') {

            $class = "Economy";
        } else {

            $class = "Business";
        }
        $input = str_replace('{airline}', $this->order->airline_txt, $input);
        $input = str_replace('{class}', $class, $input);


        $input = str_replace('{room_qty}', count($this->order->room), $input);
        $input = str_replace('{flight_meal}', $this->order->flight_meal, $input);

        $input = str_replace('{seat}', $this->order->seat, $input);
        $input = str_replace('{qty}', $this->order->qty, $input);


        $input = str_replace('{special_instruction}', $this->order->special_instruction, $input);

        $input = str_replace('{cancel_note}', $this->order->cancel_note, $input);
        //$input = str_replace('{depart}', DateHelper::toShort($this->order->depart), $input);
        //$input = str_replace('{package_name}', $this->order->package->title , $input);

        $date = $this->order->depart;
        $input = str_replace('{depart}', date("D, j M, Y", strtotime($date)), $input);
        return $input;
    }


    private function fillTour($input)
    {

        // $duration = TourHelper::formatDuration($this->tour);
        $input = str_replace('{tour_inclusions}', $this->tour->include, $input);
        $input = str_replace('{tour_exclusions}', $this->tour->exclude, $input);
        // $input = str_replace('{duration}', $duration, $input);
        // $input = str_replace('{tour_title}', $this->tour->title . '-' . TourHelper::formatDuration($this->tour), $input);
        $input = str_replace('{tour_code}', $this->tour->code, $input);
        // $input = str_replace('{tour_length}', TourHelper::formatDuration($this->tour), $input);
        // $input = str_replace('{image}',  MEDIA_URL . '/' . $this->tour->image, $input);

        //$layout = new JLayoutFile('passengers', $basePath = JPATH_ROOT .'/components/com_bookpro/layouts');
        //$html = $layout->render($this->order);
        //$html = '';
        // $passengers=$this->order->passengers;
        // foreach ($passengers as $pass){
        //     $html.=$pass->firstname.' '.$pass->lastname."<br/>";
        // }
        // $input = str_replace('{passengers}', $html, $input);

        return $input;
    }


    private function fillLogo($input)
    {
        //$config = JFactory::getConfig('logo');

        //$input = str_replace('{logo}', $logo, $input);


        return $input;
    }
}
