package org.university.electronic.suppliesmanagementsystem.Controllers;

import javafx.animation.FadeTransition;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.util.Duration;

import java.util.List;
import java.util.stream.Collectors;

// MODELS
import org.university.electronic.suppliesmanagementsystem.Models.*;

// VIEWS
import org.university.electronic.suppliesmanagementsystem.Views.AdministratorView;

// CONTROLLERS
import org.university.electronic.suppliesmanagementsystem.Controllers.LoginController;

public class AdminController {

    private final UserManager userManager;
    public final Inventory inventory;

    public AdminController() {
        this.userManager = new UserManager();
        this.inventory = new Inventory();
        userManager.loadUsers();
        inventory.loadInventory();
    }

    public UserManager getUserManager() {
        return userManager;
    }



    //  total registered users
    public int getTotalUsersCount() {
        return userManager.getUsers().size();
    }

    //  search users by username or role
    public List<User> searchUsers(String keyword) {
        if (keyword == null || keyword.isEmpty()) {
            return userManager.getUsers();
        }

        String lower = keyword.toLowerCase();

        return userManager.getUsers().stream()
                .filter(user ->
                        user.getUsername().toLowerCase().contains(lower)
                                || user.getRole().toLowerCase().contains(lower)
                )
                .collect(Collectors.toList());
    }

    //  users for table
    public List<User> getAllUsersForTable() {
        return userManager.getUsers();
    }



    public void start(Stage primaryStage) {
        Button addUserButton = new Button("Add User");
        Button deleteUserButton = new Button("Delete User");
        Button viewUsersButton = new Button("View Users");
        Button viewFinancialsButton = new Button("View Financials");
        Button signOutButton = new Button("Sign Out");

        VBox layout = new VBox(15);
        layout.setPadding(new Insets(20));
        layout.getChildren().addAll(
                addUserButton,
                deleteUserButton,
                viewUsersButton,
                viewFinancialsButton,
                signOutButton
        );

        addUserButton.setOnAction(e -> addUser(primaryStage));
        deleteUserButton.setOnAction(e -> deleteUser(primaryStage));
        viewFinancialsButton.setOnAction(e -> viewFinancials(primaryStage));
        signOutButton.setOnAction(e -> signOut(primaryStage));

        Scene adminScene = new Scene(layout, 800, 600);

        FadeTransition fadeOut = new FadeTransition(
                Duration.seconds(0.5),
                primaryStage.getScene().getRoot()
        );
        fadeOut.setFromValue(1.0);
        fadeOut.setToValue(0.0);

        fadeOut.setOnFinished(e -> {
            primaryStage.setScene(adminScene);
            FadeTransition fadeIn = new FadeTransition(
                    Duration.seconds(0.5),
                    adminScene.getRoot()
            );
            fadeIn.setFromValue(0.0);
            fadeIn.setToValue(1.0);
            fadeIn.play();
        });

        fadeOut.play();
    }

    public void signOut(Stage primaryStage) {
        LoginController loginController = new LoginController();
        loginController.start(primaryStage);
    }

    public void addUser(Stage ownerStage) {
        Stage dialog = new Stage();
        dialog.setTitle("Add User");
        dialog.initOwner(ownerStage);
        dialog.initModality(Modality.APPLICATION_MODAL);


        BorderPane root = new BorderPane();
        root.setStyle("-fx-background-color: linear-gradient(to bottom right, #020617, #0F172A);");
        root.setPadding(new Insets(20));

        // Header
        Label titleLabel = new Label("Add New User");
        titleLabel.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 24px;
            -fx-font-weight: bold;
            """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(0, 0, 20, 0));

        // Form container
        VBox formBox = new VBox(15);
        formBox.setAlignment(Pos.CENTER);
        formBox.setMaxWidth(400);

        // Username field
        Label usernameLabel = new Label("Username:");
        usernameLabel.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        TextField usernameField = new TextField();
        usernameField.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 10 15;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

        // Password field
        Label passwordLabel = new Label("Password:");
        passwordLabel.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        PasswordField passwordField = new PasswordField();
        passwordField.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 10 15;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

        // Role selection
        Label roleLabel = new Label("Role:");
        roleLabel.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        ComboBox<String> roleBox = new ComboBox<>();
        roleBox.getItems().addAll("Cashier", "Manager", "Administrator");
        roleBox.setPromptText("Select Role");
        roleBox.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 10 15;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

        // Buttons
        HBox buttonBox = new HBox(15);
        buttonBox.setAlignment(Pos.CENTER);

        Button submitButton = new Button("Add User");
        submitButton.setStyle("""
            -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
            -fx-text-fill: white;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 12 25;
            -fx-cursor: hand;
            """);

        Button cancelButton = new Button("Cancel");
        cancelButton.setStyle("""
            -fx-background-color: rgba(255,255,255,0.1);
            -fx-text-fill: #E0F7FA;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 12 25;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 12;
            -fx-cursor: hand;
            """);

        cancelButton.setOnAction(e -> dialog.close());
        buttonBox.getChildren().addAll(submitButton, cancelButton);

        // Assemble form
        formBox.getChildren().addAll(
                usernameLabel, usernameField,
                passwordLabel, passwordField,
                roleLabel, roleBox,
                buttonBox
        );

        // Submit action
        submitButton.setOnAction(e -> {
            String username = usernameField.getText().trim();
            String password = passwordField.getText().trim();
            String role = roleBox.getValue();

            if (username.isEmpty() || password.isEmpty() || role == null) {
                showAlert(Alert.AlertType.ERROR, "Error", "All fields are required");
                return;
            }

            if (userManager.findUserByUsername(username) != null) {
                showAlert(Alert.AlertType.ERROR, "Error", "Username already exists");
                return;
            }

            User newUser = new User(username, password, role);
            userManager.addUser(newUser);
            userManager.saveUsers();

            AdministratorView view =
                    (AdministratorView) ownerStage.getScene().getRoot().getUserData();
            view.refreshUsers();

            showStyledAlert(Alert.AlertType.INFORMATION, "Success", "User added successfully.");
            dialog.close();
        });

        // Final assembly
        root.setTop(header);
        root.setCenter(formBox);

        Scene dialogScene = new Scene(root, 450, 450);
        dialog.setScene(dialogScene);
        dialog.showAndWait();
    }

    public void deleteUser(Stage ownerStage) {
        Stage deleteUserWindow = new Stage();
        deleteUserWindow.setTitle("Delete User");
        deleteUserWindow.initOwner(ownerStage);
        deleteUserWindow.initModality(Modality.WINDOW_MODAL);


        BorderPane root = new BorderPane();
        root.setStyle("-fx-background-color: linear-gradient(to bottom right, #020617, #0F172A);");
        root.setPadding(new Insets(20));

        // Header
        Label titleLabel = new Label("Delete User");
        titleLabel.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 24px;
            -fx-font-weight: bold;
            """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(0, 0, 20, 0));

        // Form container
        VBox formBox = new VBox(15);
        formBox.setAlignment(Pos.CENTER);
        formBox.setMaxWidth(400);

        // Username field
        Label usernameLabel = new Label("Enter Username to Delete:");
        usernameLabel.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        TextField usernameField = new TextField();
        usernameField.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 10 15;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

        // Buttons
        HBox buttonBox = new HBox(15);
        buttonBox.setAlignment(Pos.CENTER);

        Button deleteButton = new Button("Delete User");
        deleteButton.setStyle("""
            -fx-background-color: linear-gradient(to right, #C1121F, #9B2226);
            -fx-text-fill: white;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 12 25;
            -fx-cursor: hand;
            """);

        Button cancelButton = new Button("Cancel");
        cancelButton.setStyle("""
            -fx-background-color: rgba(255,255,255,0.1);
            -fx-text-fill: #E0F7FA;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 12 25;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 12;
            -fx-cursor: hand;
            """);

        cancelButton.setOnAction(e -> deleteUserWindow.close());
        buttonBox.getChildren().addAll(deleteButton, cancelButton);

        // Assemble form
        formBox.getChildren().addAll(
                usernameLabel, usernameField,
                buttonBox
        );

        // Delete action
        deleteButton.setOnAction(event -> {
            String username = usernameField.getText();
            User user = userManager.findUserByUsername(username);
            if (user != null) {
                userManager.removeUser(user);
                userManager.saveUsers();

                AdministratorView view =
                        (AdministratorView) ownerStage.getScene().getRoot().getUserData();
                view.refreshUsers();

                showStyledAlert(Alert.AlertType.INFORMATION, "User Deleted", "User removed successfully.");
                deleteUserWindow.close();
            } else {
                showStyledAlert(Alert.AlertType.ERROR, "User Not Found", "No user with this username.");
            }
        });


        root.setTop(header);
        root.setCenter(formBox);

        deleteUserWindow.setScene(new Scene(root, 450, 300));
        deleteUserWindow.show();
    }

    public void viewFinancials(Stage ownerStage) {
        Stage viewFinancialsWindow = new Stage();
        viewFinancialsWindow.setTitle("Financial Overview");
        viewFinancialsWindow.initOwner(ownerStage);
        viewFinancialsWindow.initModality(Modality.WINDOW_MODAL);

        // Main container with same gradient as admin dashboard
        BorderPane root = new BorderPane();
        root.setStyle("-fx-background-color: linear-gradient(to bottom right, #020617, #0F172A);");
        root.setPadding(new Insets(20));

        // Header
        Label titleLabel = new Label("Financial Overview");
        titleLabel.setStyle("""
        -fx-text-fill: #4DF0F8;
        -fx-font-size: 24px;
        -fx-font-weight: bold;
        """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(0, 0, 20, 0));

        // Content container
        VBox contentBox = new VBox(20);
        contentBox.setAlignment(Pos.CENTER);
        contentBox.setMaxWidth(800);

        // Calculate financial data
        double totalIncome = inventory.getItems().stream()
                .mapToDouble(item -> item.getSellingPrice() * item.getStockLevel())
                .sum();

        double totalCosts = inventory.getItems().stream()
                .mapToDouble(item -> item.getPurchasePrice() * item.getStockLevel())
                .sum();

        double profit = totalIncome - totalCosts;

        // Create bigger financial cards with larger text
        // Income card
        VBox incomeCard = createFinancialCard("Total Income",
                String.format("$%.2f", totalIncome),
                "#00B4D8",
                28); // Larger font size

        // Costs card
        VBox costsCard = createFinancialCard("Total Costs",
                String.format("$%.2f", totalCosts),
                "#FF6B6B",
                28); // Larger font size

        // Profit card
        String profitColor = profit >= 0 ? "#4ADE80" : "#FF6B6B";
        VBox profitCard = createFinancialCard("Profit",
                String.format("$%.2f", profit),
                profitColor,
                28);


        HBox cardsRow = new HBox(30, incomeCard, costsCard, profitCard);
        cardsRow.setAlignment(Pos.CENTER);


        incomeCard.setPrefWidth(220);
        incomeCard.setPrefHeight(180);
        costsCard.setPrefWidth(220);
        costsCard.setPrefHeight(180);
        profitCard.setPrefWidth(220);
        profitCard.setPrefHeight(180);

        // Close button
        Button closeButton = new Button("Close");
        closeButton.setStyle("""
        -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
        -fx-text-fill: white;
        -fx-font-size: 16px;
        -fx-font-weight: bold;
        -fx-background-radius: 12;
        -fx-padding: 15 40;
        -fx-cursor: hand;
        """);

        closeButton.setOnAction(e -> viewFinancialsWindow.close());

        // Assemble content with proper spacing
        contentBox.getChildren().addAll(cardsRow, closeButton);
        VBox.setMargin(closeButton, new Insets(30, 0, 0, 0));


        root.setTop(header);
        root.setCenter(contentBox);

        // Make window larger
        viewFinancialsWindow.setScene(new Scene(root, 900, 500));
        viewFinancialsWindow.show();
    }

    private VBox createFinancialCard(String title, String value, String color, int valueFontSize) {
        VBox card = new VBox(15); // More spacing between elements
        card.setAlignment(Pos.CENTER);
        card.setPadding(new Insets(30, 25, 30, 25)); // More padding
        card.setStyle(String.format("""
        -fx-background-color: rgba(2, 6, 23, 0.9);
        -fx-background-radius: 20;
        -fx-border-color: %s;
        -fx-border-radius: 20;
        -fx-border-width: 3px;
        """, color));

        Label titleLabel = new Label(title);
        titleLabel.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 20px; -fx-font-weight: bold;");

        Label valueLabel = new Label(value);
        valueLabel.setStyle(String.format("""
        -fx-text-fill: %s;
        -fx-font-size: %dpx;
        -fx-font-weight: bold;
        -fx-font-family: 'Arial';
        """, color, valueFontSize));

        // Add word wrap to handle long numbers
        valueLabel.setWrapText(true);
        valueLabel.setTextAlignment(javafx.scene.text.TextAlignment.CENTER);

        card.getChildren().addAll(titleLabel, valueLabel);
        return card;
    }

    private void showStyledAlert(Alert.AlertType type, String title, String message) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);

        // Apply custom styles to alert dialog
        DialogPane dialogPane = alert.getDialogPane();
        dialogPane.setStyle("""
            -fx-background-color: linear-gradient(to bottom right, #020617, #0F172A);
            -fx-border-color: #00B4D8;
            -fx-border-width: 2px;
            -fx-border-radius: 10;
            """);

        // Style the content text
        dialogPane.lookup(".content.label").setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        // Style the header
        dialogPane.lookup(".header-panel").setStyle("-fx-background-color: transparent;");
        dialogPane.lookup(".header-panel .label").setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 18px;
            -fx-font-weight: bold;
            """);

        // Style buttons
        ButtonBar buttonBar = (ButtonBar) dialogPane.lookup(".button-bar");
        if (buttonBar != null) {
            buttonBar.getButtons().forEach(btn -> {
                if (btn instanceof Button) {
                    Button button = (Button) btn;
                    button.setStyle("""
                        -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                        -fx-text-fill: white;
                        -fx-font-weight: bold;
                        -fx-background-radius: 8;
                        -fx-padding: 8 16;
                        -fx-cursor: hand;
                        """);
                }
            });
        }

        alert.showAndWait();
    }

    private void showAlert(Alert.AlertType type, String title, String message) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}