            /* Styles for the navigation menu */
            ul.menu {
                list-style-type: none;
                margin: 0;
                padding: 0;
                overflow: hidden;
                background-color: #333;
            }
            ul.menu li a {
                display: block;
                color: white;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
            }
            ul.menu li p {
                padding-left:10px;
                display: block;
                color: white;
                text-align: center;
                text-decoration: none;
            }
            ul.menu li a:hover {
                background-color: #111;
            }
            /* Styles for the dropdown content */
            li.dropdown {
                display: inline-block;
            }
            /* Logout button */
            .custom-btn {
                margin-left:15px;
                margin-top:5px;
                margin-right:10px;
                background-color: #007BFF; /* Custom background color */
                color: #ffffff; /* Text color */
                border: none; /* Remove borders */
                cursor: pointer; /* Cursor style */
                padding: 10px 20px; /* Padding for the button */
                border-radius: 5px; /* Rounded corners */
                font-size: 16px; /* Font size */
                transition: background-color 0.3s; /* Transition for hover effect */
            }

            .custom-btn:focus {
                outline: none; /* Remove focus outline */
            }

            .custom-btn:hover {
                background-color: #0056b3; /* Darken background on hover */
            }
