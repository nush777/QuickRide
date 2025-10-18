# QuickRide
This Online Bus Ticket Booking System is a web-based application designed to provide a seamless and efficient platform for booking bus tickets. It simplifies the process for passengers and streamlines management tasks for administrators. With a user-friendly interface and robust functionality, this system enhances the overall experience of bus travel.

## 🛠️ Technologies Used

    Frontend: HTML, Tailwind - CSS, Raw PHP
    Backend: PHP
    Database: MySQL 
    Additional Tools: XAMMP(DBMS)
## 🚀 Features
For Passengers:

    🔍 Search Buses:
    User can Search Buses From --> To Location with Coach Type and Date Based.

    🚌 Dynamic Bus:
     For Dynamic Bus Time, We work with two predefined location_time for a specific Location with predefined location_level(e.x 1,2,3...) 
     so that user can search from specific bus stop location.
    (E.X  Dhaka - Jessore - Benapole location buses .User can search

     Dhaka    -> Benapole
     Dhaka    -> Jessore
     Jessore  -> Benapole
     Jessore  -> Dhaka
     Benapole -> Jessore
     Benapole -> Dhaka
     Benapole -> Jessore etc
     )
## Good Looking UI with Dynamic Seat Map:
     - we work with AC(24 seats) and Non AC(32 Seats) So that we handle dynamic seat map for ac and Non Ac Buses.
     - Look At Below Screen Shots for ticketPanel.php file. 
     - User can select one or more than one seats at a time .
     - Also they can cancel those selected seats and Confirm.
     - User Can't select previous booked seats.
     - We Handle dynamic Ticket Price calculation. 
