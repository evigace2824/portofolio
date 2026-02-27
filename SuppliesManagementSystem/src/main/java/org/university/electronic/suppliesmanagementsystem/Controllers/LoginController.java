package org.university.electronic.suppliesmanagementsystem.Controllers;

import javafx.animation.FadeTransition;
import javafx.application.Application;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.effect.DropShadow;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.paint.LinearGradient;
import javafx.scene.paint.CycleMethod;
import javafx.scene.paint.Stop;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.stage.Stage;
import javafx.util.Duration;
// MODELS
import org.university.electronic.suppliesmanagementsystem.Models.Inventory;
import org.university.electronic.suppliesmanagementsystem.Models.Manager;
import org.university.electronic.suppliesmanagementsystem.Models.User;
import org.university.electronic.suppliesmanagementsystem.Models.UserManager;

// VIEWS
import org.university.electronic.suppliesmanagementsystem.Views.AdministratorView;
import org.university.electronic.suppliesmanagementsystem.Views.CashierDashboard;
import org.university.electronic.suppliesmanagementsystem.Views.ManagerDashboard;

// CONTROLLERS


public class LoginController extends Application {

    private UserManager userManager;

    @Override
    public void start(Stage primaryStage) {

        userManager = new UserManager();


        BorderPane root = new BorderPane();



        Image bgImage = new Image(
                getClass().getResourceAsStream(
                        "/org/university/electronic/suppliesmanagementsystem/image2.png"
                )
        );
        BackgroundSize bgSize = new BackgroundSize(
                100, 100, true, true, true, true
        );

        BackgroundImage backgroundImage = new BackgroundImage(
                bgImage,
                BackgroundRepeat.NO_REPEAT,
                BackgroundRepeat.NO_REPEAT,
                BackgroundPosition.CENTER,
                bgSize
        );

        root.setBackground(new Background(backgroundImage));


        VBox layout = new VBox(25);
        layout.setPadding(new Insets(50, 40, 50, 40));
        layout.setAlignment(Pos.CENTER);
        layout.setPrefWidth(500);
        layout.setMinHeight(600);
        layout.setMaxWidth(500);





        ImageView logoImageView = new ImageView(
                new Image(
                        getClass().getResourceAsStream(
                                "/org/university/electronic/suppliesmanagementsystem/logo_epoka_uni.png"
                        )
                )
        );
        logoImageView.setFitWidth(160);
        logoImageView.setFitHeight(160);
        logoImageView.setPreserveRatio(true);


        DropShadow logoShadow = new DropShadow();
        logoShadow.setRadius(12);
        logoShadow.setOffsetY(4);
        logoShadow.setColor(Color.rgb(0, 0, 0, 0.25));
        logoImageView.setEffect(logoShadow);


        Label titleLabel = new Label(" Supplies Management System ");
        titleLabel.setFont(Font.font("Times New Roman", FontWeight.BOLD, 28));

        // Gradient text effect
        Stop[] stops = new Stop[] {
                new Stop(0, Color.web("#4DF0F8")),
                new Stop(1, Color.web("#00B4D8"))
        };
        LinearGradient gradient = new LinearGradient(0, 0, 1, 0, true, CycleMethod.NO_CYCLE, stops);
        titleLabel.setTextFill(gradient);

        // Text shadow for better readability
        DropShadow textShadow = new DropShadow();
        textShadow.setRadius(3);
        textShadow.setOffsetY(2);
        textShadow.setColor(Color.rgb(0, 0, 0, 0.4));
        titleLabel.setEffect(textShadow);


        Label usernameLabel = new Label("Username:");
        usernameLabel.setFont(Font.font("Arial", FontWeight.BOLD, 14));
        usernameLabel.setTextFill(Color.web("#E0F7FA"));
        usernameLabel.setStyle("-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.3), 2, 0.3, 0, 1);");

        TextField usernameField = new TextField();
        usernameField.setPromptText("Enter your username");
        styleInput(usernameField);

        Label passwordLabel = new Label("Password:");
        passwordLabel.setFont(Font.font("Arial", FontWeight.BOLD, 14));
        passwordLabel.setTextFill(Color.web("#E0F7FA"));
        passwordLabel.setStyle("-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.3), 2, 0.3, 0, 1);");

        PasswordField passwordField = new PasswordField();
        passwordField.setPromptText("Enter your password");
        styleInput(passwordField);

        TextField visiblePasswordField = new TextField();
        visiblePasswordField.setPromptText("Enter your password");
        styleInput(visiblePasswordField);

        visiblePasswordField.setManaged(false);
        visiblePasswordField.setVisible(false);


        visiblePasswordField.textProperty().bindBidirectional(passwordField.textProperty());

        CheckBox showPasswordCheckBox = new CheckBox("Show Password");
        showPasswordCheckBox.setTextFill(Color.web("#E0F7FA"));
        showPasswordCheckBox.setStyle("""
    -fx-font-size: 12px;
    -fx-font-weight: bold;
""");

        showPasswordCheckBox.setOnAction(e -> {
            if (showPasswordCheckBox.isSelected()) {
                passwordField.setVisible(false);
                passwordField.setManaged(false);

                visiblePasswordField.setVisible(true);
                visiblePasswordField.setManaged(true);
            } else {
                passwordField.setVisible(true);
                passwordField.setManaged(true);

                visiblePasswordField.setVisible(false);
                visiblePasswordField.setManaged(false);
            }
        });

        /* ================= ERROR LABEL ================= */
        Label errorLabel = new Label();
        errorLabel.setTextFill(Color.web("#FF6B6B"));
        errorLabel.setFont(Font.font("Arial", FontWeight.BOLD, 13));
        errorLabel.setStyle("""
                -fx-effect: dropshadow(gaussian, rgba(255,107,107,0.3), 3, 0.3, 0, 1);
                -fx-padding: 5;
                """);
        errorLabel.setMaxWidth(300);
        errorLabel.setWrapText(true);
        errorLabel.setAlignment(Pos.CENTER);

        /* ================= LOGIN BUTTON ================= */
        Button loginButton = new Button("LOGIN");
        loginButton.setPrefWidth(200);
        loginButton.setPrefHeight(50);

        // Enhanced button styling with hover effect
        loginButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                -fx-text-fill: white;
                -fx-font-size: 16px;
                -fx-font-weight: bold;
                -fx-background-radius: 25;
                -fx-cursor: hand;
                -fx-effect: dropshadow(gaussian, rgba(0,180,216,0.4), 10, 0.3, 0, 3);
                """);

        // Hover effect
        loginButton.setOnMouseEntered(e -> loginButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #00C6FB, #0096D6);
                -fx-text-fill: white;
                -fx-font-size: 16px;
                -fx-font-weight: bold;
                -fx-background-radius: 25;
                -fx-cursor: hand;
                -fx-effect: dropshadow(gaussian, rgba(0,198,251,0.5), 12, 0.4, 0, 4);
                """));

        loginButton.setOnMouseExited(e -> loginButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                -fx-text-fill: white;
                -fx-font-size: 16px;
                -fx-font-weight: bold;
                -fx-background-radius: 25;
                -fx-cursor: hand;
                -fx-effect: dropshadow(gaussian, rgba(0,180,216,0.4), 10, 0.3, 0, 3);
                """));

        // Press effect
        loginButton.setOnMousePressed(e -> loginButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #0096C7, #005885);
                -fx-text-fill: rgba(255,255,255,0.9);
                -fx-font-size: 16px;
                -fx-font-weight: bold;
                -fx-background-radius: 25;
                -fx-cursor: hand;
                -fx-effect: dropshadow(gaussian, rgba(0,180,216,0.3), 8, 0.2, 0, 2);
                -fx-translate-y: 1;
                """));

        loginButton.setOnMouseReleased(e -> loginButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #00C6FB, #0096D6);
                -fx-text-fill: white;
                -fx-font-size: 16px;
                -fx-font-weight: bold;
                -fx-background-radius: 25;
                -fx-cursor: hand;
                -fx-effect: dropshadow(gaussian, rgba(0,198,251,0.5), 12, 0.4, 0, 4);
                -fx-translate-y: 0;
                """));

        loginButton.setOnAction(e -> {
            String password = passwordField.isVisible()
                    ? passwordField.getText()
                    : visiblePasswordField.getText();

            handleLogin(
                    usernameField.getText(),
                    password,
                    errorLabel,
                    primaryStage
            );
        });


        passwordField.setOnAction(e -> loginButton.fire());

        /* ================= ADD ALL ================= */
        layout.getChildren().addAll(
                logoImageView,
                new Region() {{
                    setPrefHeight(10);
                }},
                titleLabel,
                new Region() {{
                    setPrefHeight(20);
                }},
                usernameLabel,
                usernameField,
                new Region() {{
                    setPrefHeight(5);
                }},
                passwordLabel,
                new StackPane(passwordField, visiblePasswordField),
                showPasswordCheckBox,
                new Region() {{
                    setPrefHeight(15);
                }},
                loginButton,
                errorLabel
        );

        /* ================= SET LOGIN CARD TO LEFT SIDE ================= */
        StackPane leftContainer = new StackPane(layout);
        leftContainer.setPadding(new Insets(20));
        StackPane.setAlignment(layout, Pos.CENTER_LEFT);
        root.setLeft(leftContainer);

        Scene scene = new Scene(root);
        primaryStage.setScene(scene);
        primaryStage.setTitle("EPOKA Electronics - Login");
        primaryStage.setMaximized(true);
        primaryStage.show();
    }

    /* ================= INPUT STYLE ================= */
    private void styleInput(TextField field) {
        field.setPrefHeight(45);
        field.setMaxWidth(320);
        field.setStyle("""
                -fx-font-size: 15px;
                -fx-background-color: rgba(255, 255, 255, 0.95);
                -fx-background-radius: 20;
                -fx-border-radius: 20;
                -fx-border-color: #B0E0E6;
                -fx-border-width: 2;
                -fx-padding: 0 18 0 18;
                -fx-text-fill: #2C3E50;
                -fx-font-weight: 500;
                -fx-effect: dropshadow(gaussian, rgba(0,0,0,0.08), 3, 0.2, 0, 1);
                """);

        // Focus effect
        field.focusedProperty().addListener((obs, oldVal, newVal) -> {
            if (newVal) {
                field.setStyle("""
                    -fx-font-size: 15px;
                    -fx-background-color: white;
                    -fx-background-radius: 20;
                    -fx-border-radius: 20;
                    -fx-border-color: #00B4D8;
                    -fx-border-width: 2.5;
                    -fx-padding: 0 18 0 18;
                    -fx-text-fill: #2C3E50;
                    -fx-font-weight: 500;
                    -fx-effect: dropshadow(gaussian, rgba(0,180,216,0.2), 5, 0.3, 0, 2);
                    """);
            } else {
                field.setStyle("""
                    -fx-font-size: 15px;
                    -fx-background-color: rgba(255, 255, 255, 0.95);
                    -fx-background-radius: 20;
                    -fx-border-radius: 20;
                    -fx-border-color: #B0E0E6;
                    -fx-border-width: 2;
                    -fx-padding: 0 18 0 18;
                    -fx-text-fill: #2C3E50;
                    -fx-font-weight: 500;
                    -fx-effect: dropshadow(gaussian, rgba(0,0,0,0.08), 3, 0.2, 0, 1);
                    """);
            }
        });
    }


    public void handleLogin(String username, String password,
                            Label errorLabel, Stage primaryStage) {

        User user = userManager.findUserByUsername(username);

        if (user != null && user.getPassword().equals(password)) {
            errorLabel.setText("");
            navigateToDashboard(user, primaryStage);
        } else {
            errorLabel.setText("Invalid username or password. Please try again.");

            FadeTransition ft = new FadeTransition(Duration.seconds(0.4), errorLabel);
            ft.setFromValue(0);
            ft.setToValue(1);
            ft.play();

            // Shake animation for wrong credentials
            FadeTransition shake = new FadeTransition(Duration.seconds(0.1), errorLabel);
            shake.setFromValue(1);
            shake.setToValue(0.7);
            shake.setCycleCount(4);
            shake.setAutoReverse(true);
            shake.play();
        }
    }

    /* ================= NAVIGATION ================= */
    private void navigateToDashboard(User user, Stage primaryStage) {

        FadeTransition fadeOut = new FadeTransition(
                Duration.seconds(0.6),
                primaryStage.getScene().getRoot()
        );
        fadeOut.setFromValue(1);
        fadeOut.setToValue(0);

        fadeOut.setOnFinished(e -> {

            switch (user.getRole()) {
                case "Administrator" -> {
                    AdminController adminController = new AdminController();
                    new AdministratorView(adminController)
                            .display(primaryStage);
                }
                case "Manager" -> {
                    Manager manager = new Manager(
                            user.getUsername(),
                            user.getPassword(),
                            new Inventory(),
                            null,
                            null
                    );
                    new ManagerDashboard(manager)
                            .showManagerDashboard(primaryStage);
                }
                case "Cashier" -> {
                    Inventory inventory = new Inventory();
                    new CashierDashboard(inventory, user.getUsername())
                            .showCashierDashboard(primaryStage);
                }
            }

            FadeTransition fadeIn = new FadeTransition(
                    Duration.seconds(0.6),
                    primaryStage.getScene().getRoot()
            );
            fadeIn.setFromValue(0);
            fadeIn.setToValue(1);
            fadeIn.play();
        });

        fadeOut.play();
    }

    /* ================= DARK OVERLAY CLASS ================= */
    private class RectangleOverlay extends StackPane {
        public RectangleOverlay() {
            setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom,
                    rgba(0, 0, 0, 0.4),
                    rgba(0, 0, 0, 0.3)
                );
                """);
            setAlignment(Pos.CENTER);
        }
    }
}