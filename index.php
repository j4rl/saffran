<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>


    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        
        <nav>
            <img src="assets/logo.png" alt="Logo" height="70px">
            <div class="logo">Saffran</div>
            <ul>
                <li><a href="#">Statistics</a></li>
                <li><a href="#">New</a></li>
                <li><a href="#">Ongoing</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <!-- This section should show data about cases submitted, how many open cases how many closed, average a week and other relevant data -->
            <h1>Statistics</h1>
            <!-- Make the statistic like a dashboard -->
            <div class="dashboard">
                <!-- Make open and closed cases % of total cases in the same box -->
                 <div class="card" id="open-closed">
                    <h2>Open cases</h2>
                    <p>3</p>
                    <h2>Closed cases</h2>
                    <p>10</p>
                 </div>

                <div class="card">
                    <h2>Average cases a week</h2>
                    <p>2</p>
                </div>
                <div class="card">
                    <h2>Active users</h2>
                    <p>5</p>
                </div>

            </div>
            
        </section>
        <section>
            <h1>New case</h1>
            <!-- Make this section a submit form for submitting support tickets for actions with the union -->
            <form action="submit.php" method="post">
                <fieldset>
                    <legend>Member data</legend>
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                    <label for="ismember">Member</label>
                    <input type="checkbox" name="ismember" id="ismember">
                </fieldset>

                <label for="message">Statement:</label>
                <textarea name="message" id="message" required rows="10"></textarea>
                <div class="btn"><button type="submit">Submit</button></div>
                
            </form>
         </section>
        <section>
            <h1>Ongoing</h1>
            <!-- Show ongoing cases from database, default is cases submitted by the logged in user. Make sure the active user assigned to the case is showed -->
            <table>
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Member</th>
                        <th>Statement</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>John Doe is a member of the union and has been a member for 5 years. He has been a good member and has paid his dues on time.</td>
                        <td>John Doe</td>
                        <td>Active</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Doe</td>
                        <td>Jane Doe is a member of the union and has been a member for 5 years. She has been a good member and has paid her dues on time.</td>
                        <td>John Doe</td>
                        <td>Active</td>
                    </tr>
                </tbody>
            </table>

        </section>
        <section>
            <h1>Services</h1>
            <!-- Show login link to sverigeslarare.se, av.se, mintrygghet.se, swedish laws and similar resources -->
            <ul>
                <li><a href="https://www.sverigeslarare.se">Sveriges Lärare</a></li>
                <li><a href="https://www.av.se">Arbetsmiljöverket</a></li>
                <li><a href="https://www.mintrygghet.se">Min Trygghet</a></li>
                <li><a href="https://www.lagboken.se">Lagboken</a></li>
            </ul>
        </section>
        <section>
            <h1>Login</h1>
            <!-- Show admin panel for the union. This should be a login form for the admin panel -->
            <form action="admin.php" method="post">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <button type="submit">Login</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2025</p>
    </footer>
</body>
</html>
<script>
    // Get all the sections
    const sections = document.querySelectorAll('section');
    // Get all the links
    const links = document.querySelectorAll('nav a');

    // Loop through the links
    links.forEach((link, index) => {
        // Add an event listener to each link
        link.addEventListener('click', () => {
            // Loop through the sections
            sections.forEach((section, sectionIndex) => {
                if (index === sectionIndex) {
                    section.style.transform = 'rotateX(0deg)';
                } else {
                    section.style.transform = 'rotateX(-180deg)';
                }
            });
        });
    });

    sections[0].style.transform = 'rotateX(0deg)';
    sections.forEach((section, index) => {
        if (index !== 0) {
            section.style.transform = 'rotateX(-180deg)';
        }
    });

    // Get #open-closed and change the value in the gradient to the value of open cases. background: conic-gradient(rgba(56, 190, 29, 0.918) 0%,rgba(56, 190, 29, 0.918) 33%, rgb(211, 19, 19) 33%,rgb(211, 19, 19) 100%);

    const openClosed = document.getElementById('open-closed');
    //get the open cases from the text in the #open-closed element


    const openCases = parseInt(openClosed.children[1].textContent);
    const closedCases = parseInt(openClosed.children[3].textContent);
    const totalCases = openCases + closedCases;
    const openCasesPercentage = (openCases / totalCases) * 100;
    openClosed.style.background = `conic-gradient(rgba(218, 162, 44, 0.2) 0%,rgba(218, 162, 44, 0.2) ${openCasesPercentage}%, rgb(81, 185, 40,.2) ${openCasesPercentage}%,rgb(81, 185, 40,.2) 100%)`;




</script>