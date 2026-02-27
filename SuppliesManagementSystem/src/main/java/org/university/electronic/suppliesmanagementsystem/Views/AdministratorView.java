package org.university.electronic.suppliesmanagementsystem.Views;

import org.university.electronic.suppliesmanagementsystem.Controllers.AdminController;
import org.university.electronic.suppliesmanagementsystem.Models.User;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.collections.transformation.FilteredList;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.stage.Screen;
import javafx.stage.Stage;
import org.university.electronic.suppliesmanagementsystem.Controllers.LoginController;

public class AdministratorView {

    private final AdminController adminController;

    private TableView<User> tableView;
    private ObservableList<User> masterData;
    private Label usersCountLabel;
    private VBox centerContent;
    private VBox countCard;

    public AdministratorView(AdminController adminController) {
        this.adminController = adminController;
    }

    public void display(Stage primaryStage) {

        BorderPane root = new BorderPane();
        root.setStyle("""
            -fx-background-color: linear-gradient(to bottom right, #020617, #0F172A);
        """);
        root.setUserData(this);

        /* ================= SIDEBAR ================= */
        Label menuTitle = new Label("Dashboard");
        menuTitle.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 22px;
            -fx-font-weight: bold;
        """);

        Button addUserBtn = createMenuButton("Add User");
        Button deleteUserBtn = createMenuButton("Delete User");
        Button financeBtn = createMenuButton("View Financials");
        Button signOutBtn = createMenuButton("Sign Out");

        signOutBtn.setStyle("""
            -fx-background-color: linear-gradient(to right, #C1121F, #9B2226);
            -fx-text-fill: white;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 10 15;
        """);

        addUserBtn.setOnAction(e -> showAddUserCard());
        deleteUserBtn.setOnAction(e -> showDeleteUserCard());
        financeBtn.setOnAction(e -> showFinancialsCard());
        signOutBtn.setOnAction(e -> signOut(primaryStage));

        Region spacer = new Region();
        VBox.setVgrow(spacer, Priority.ALWAYS);

        VBox sidebar = new VBox(25, menuTitle, addUserBtn, deleteUserBtn, financeBtn, spacer, signOutBtn);
        sidebar.setPadding(new Insets(30));
        sidebar.setPrefWidth(250);
        sidebar.setStyle("-fx-background-color: rgba(2,6,23,0.95);");

        /* ================= HEADER ================= */
        Label titleLabel = new Label("Administrator Dashboard");
        titleLabel.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 26px;
            -fx-font-weight: bold;
        """);

        TextField searchField = new TextField();
        searchField.setPromptText("Search users...");

        HBox header = new HBox(20, titleLabel, searchField);
        header.setAlignment(Pos.CENTER_LEFT);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2,6,23,0.9);");

        /* ================= USERS COUNT ================= */
        usersCountLabel = new Label();
        usersCountLabel.setStyle("""
            -fx-text-fill: #00E5FF;
            -fx-font-size: 26px;
            -fx-font-weight: bold;
        """);

        countCard = new VBox(
                new Label("Registered Users"),
                usersCountLabel
        );
        countCard.setPadding(new Insets(18));
        countCard.setSpacing(8);
        countCard.setStyle("""
            -fx-background-color: rgba(2,6,23,0.9);
            -fx-background-radius: 15;
        """);
        countCard.getChildren().get(0).setStyle("-fx-text-fill: #E0F7FA;");

        /* ================= TABLE ================= */
        tableView = new TableView<>();
        tableView.setStyle(""" 
                -fx-background-radius: 15px;
                -fx-background-color: #020617; 
                -fx-control-inner-background: #020617; 
                -fx-table-cell-border-color: transparent; 
                -fx-selection-bar: rgba(0,180,216,0.35); 
                -fx-selection-bar-non-focused: rgba(0,180,216,0.25); 
                """);
        /* Username */
        TableColumn<User, String> usernameCol = new TableColumn<>("Username");
        usernameCol.setPrefWidth(250);
        usernameCol.setCellValueFactory(data -> new javafx.beans.property.SimpleStringProperty(data.getValue().getUsername()) );
        /* Role */
        TableColumn<User, String> roleCol = new TableColumn<>("Role");
        roleCol.setPrefWidth(150);
        roleCol.setCellValueFactory(data -> new javafx.beans.property.SimpleStringProperty(data.getValue().getRole()) );

        /* Password  */
        TableColumn<User, Void> passwordCol = new TableColumn<>("Password");
        passwordCol.setPrefWidth(200);
        passwordCol.setCellFactory(col -> new TableCell<>() {
            private boolean visible = false;
            private final Label passwordLabel = new Label("****");
            private final Button toggleBtn = new Button("Show");
            private final HBox box = new HBox(8, passwordLabel, toggleBtn);
            { passwordLabel.setStyle("-fx-text-fill: #38BDF8; -fx-font-weight: bold;");
                toggleBtn.setStyle(""" 
                        -fx-background-color: #00B4D8; 
                        -fx-text-fill: white; -fx-font-size: 11px; 
                        -fx-background-radius: 8; 
                        """);
                toggleBtn.setOnAction(e -> { User user = getTableView().getItems().get(getIndex());
                    visible = !visible;
                    passwordLabel.setText(visible ? user.getPassword() : "****");
                    toggleBtn.setText(visible ? "Hide" : "Show"); }); }
            @Override
            protected void updateItem(Void item, boolean empty) { super.updateItem(item, empty);
            if (empty) {
                setGraphic(null);
            } else {
                setGraphic(box); }
        }
        });

        tableView.getColumns().addAll(usernameCol, roleCol,passwordCol);

        masterData = FXCollections.observableArrayList(
                adminController.getUserManager().getUsers()
        );

        FilteredList<User> filtered = new FilteredList<>(masterData, p -> true);

        searchField.textProperty().addListener((obs, o, n) -> {
            filtered.setPredicate(u ->
                    n == null || n.isEmpty()
                            || u.getUsername().toLowerCase().contains(n.toLowerCase())
                            || u.getRole().toLowerCase().contains(n.toLowerCase())
            );
            updateUserCount(filtered.size());
        });

        tableView.setItems(filtered);

        centerContent = new VBox(25, countCard, tableView);
        centerContent.setPadding(new Insets(25));
        VBox.setVgrow(tableView, Priority.ALWAYS);

        root.setLeft(sidebar);
        root.setTop(header);
        root.setCenter(centerContent);

        Scene scene = new Scene(root, 1200, 750);
        primaryStage.setScene(scene);
        primaryStage.setTitle("Administrator Dashboard");
        primaryStage.setWidth(Screen.getPrimary().getBounds().getWidth());
        primaryStage.setHeight(Screen.getPrimary().getBounds().getHeight());
        primaryStage.show();

        updateUserCount(masterData.size());
    }

    private void showAddUserCard() {
        VBox formBox = new VBox(15);
        formBox.setAlignment(Pos.CENTER_LEFT);
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


        Button add = new Button("Add User");
        add.setStyle("""
            -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
            -fx-text-fill: white;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 12 25;
            -fx-cursor: hand;
            """);
        add.setOnAction(e -> {
            String username = usernameField.getText().trim();
            String password = passwordField.getText().trim();
            String role = roleBox.getValue();

            if (username.isEmpty() || password.isEmpty() || role == null) {
                new Alert(
                        Alert.AlertType.ERROR,
                        "All fields are required"
                ).showAndWait();
                return;
            }
            if (adminController.getUserManager().findUserByUsername(username) != null) {
                new Alert(
                        Alert.AlertType.ERROR,
                        "Username already exists"
                ).showAndWait();
                return;
            }

            adminController.getUserManager().addUser(
                    new User(username, password, role)
            );
            adminController.getUserManager().saveUsers();
            refreshUsers();
        });
        buttonBox.getChildren().add(add);


        formBox.getChildren().addAll(
                usernameLabel, usernameField,
                passwordLabel, passwordField,
                roleLabel, roleBox,
                buttonBox
        );

        centerContent.getChildren().setAll(createBackButton(),createCard("Add User", formBox, add));
    }

    private void showDeleteUserCard() {

        Label usernameLabel = new Label("Enter Username to Delete:");
        usernameLabel.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        TextField username = new TextField();
        username.setPromptText("Username");
        username.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 10 15;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

        Button delete = new Button("Delete User");
        delete.setStyle("""
            -fx-background-color: linear-gradient(to right, #C1121F, #9B2226);
            -fx-text-fill: white;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 12 25;
            -fx-cursor: hand;
            """);

        delete.setOnAction(e -> {
            User u = adminController.getUserManager()
                    .findUserByUsername(username.getText());

            if (u != null) {
                adminController.getUserManager().removeUser(u);
                adminController.getUserManager().saveUsers();
                refreshUsers();
            } else {
                new Alert(
                        Alert.AlertType.ERROR,
                        "User not found"
                ).showAndWait();
            }
        });

        centerContent.getChildren().setAll(createBackButton(),createCard("Delete User", usernameLabel,username, delete));
    }

    private void showFinancialsCard() {

        double income = adminController.inventory.getItems().stream()
                .mapToDouble(i -> i.getSellingPrice() * i.getStockLevel())
                .sum();

        double costs = adminController.inventory.getItems().stream()
                .mapToDouble(i -> i.getPurchasePrice() * i.getStockLevel())
                .sum();

        double profit = income - costs;

        String title = new String("Financial Overview");

        VBox incomeCard = createFinancialCard("Total Income",
                String.format("$%.2f", income),
                "#00B4D8",
                28); // Larger font size

        // Costs card
        VBox costsCard = createFinancialCard("Total Costs",
                String.format("$%.2f", costs),
                "#FF6B6B",
                28); // Larger font size

        // Profit card
        String profitColor = profit >= 0 ? "#4ADE80" : "#FF6B6B";
        VBox profitCard = createFinancialCard("Profit",
                String.format("$%.2f", profit),
                profitColor,
                28); // Larger font size

        // Add spacing between cards
        HBox cardsRow = new HBox(30, incomeCard, costsCard, profitCard);
        cardsRow.setAlignment(Pos.CENTER);

        HBox.setHgrow(incomeCard, Priority.ALWAYS);
        HBox.setHgrow(costsCard, Priority.ALWAYS);
        HBox.setHgrow(profitCard, Priority.ALWAYS);

        incomeCard.setMaxWidth(Double.MAX_VALUE);
        costsCard.setMaxWidth(Double.MAX_VALUE);
        profitCard.setMaxWidth(Double.MAX_VALUE);

        centerContent.getChildren().setAll(createBackButton(),createCard(title, cardsRow));
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

    private VBox createCard(String title, Node... nodes) {
        Label titleLabel = new Label(title);
        titleLabel.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 22px;
            -fx-font-weight: bold;
        """);

        VBox box = new VBox(15, titleLabel);
        box.setPadding(new Insets(25));
        box.setStyle("""
            -fx-background-color: rgba(2,6,23,0.9);
            -fx-background-radius: 18;
        """);

        box.getChildren().addAll(nodes);
        return box;
    }

    public void refreshUsers() {
        masterData.setAll(adminController.getUserManager().getUsers());
        updateUserCount(masterData.size());

    }

    private void updateUserCount(int count) {
        usersCountLabel.setText(String.valueOf(count));
    }

    private void showDashboardView() {
        centerContent.getChildren().setAll(
                centerContent.getChildren().get(0),
                countCard,
                tableView
        );

    }

    private Button createMenuButton(String text) {
        Button btn = new Button(text);
        btn.setMaxWidth(Double.MAX_VALUE);
        btn.setStyle("""
            -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
            -fx-text-fill: white;
            -fx-font-weight: bold;
            -fx-background-radius: 12;
            -fx-padding: 10 15;
        """);
        return btn;
    }

    private Button createBackButton() {
        Button back = new Button("← Go Back");
        back.setOnAction(e -> showDashboardView());

        back.setStyle("""
        -fx-background-color: transparent;
        -fx-text-fill: #4DF0F8;
        -fx-font-size: 14px;
        -fx-font-weight: bold;
    """);

        return back;
    }

    private void signOut(Stage stage) {
        stage.close();
        new LoginController().start(new Stage());
    }
}
