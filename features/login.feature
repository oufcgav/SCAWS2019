Feature: logging in to the site

  Scenario: restricted access when not logged in
    When I view the table
    Then I am redirected to the login page

  Scenario: logging in successfully
    When I login
    And I view the table
    Then I am able to see the table
