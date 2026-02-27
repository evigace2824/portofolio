package org.university.electronic.suppliesmanagementsystem.Main;

import javafx.application.Application;
import javafx.stage.Stage;
import org.university.electronic.suppliesmanagementsystem.Controllers.LoginController;

public class StartProgram extends Application {

    //entry point for the JavaFX application
    public static void main(String[] args) {
        launch(args);
    }

    @Override
    public void start(Stage primaryStage) {
        //creates the main view for the application
        primaryStage.setTitle(" Supplies Management System - SWE 3");
        primaryStage.setMaximized(true);

        //starts the login screen as the first step of the app
        showLoginScreen(primaryStage);
    }

    //method to show login screen
    private void showLoginScreen(Stage primaryStage) {
        //creates a new LoginController instance
        LoginController loginController = new LoginController();

        //launches the login screen
        loginController.start(primaryStage); //this will show the login form
    }
}
