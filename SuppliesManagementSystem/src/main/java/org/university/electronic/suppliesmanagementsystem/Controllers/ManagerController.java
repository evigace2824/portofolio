package org.university.electronic.suppliesmanagementsystem.Controllers;

import org.university.electronic.suppliesmanagementsystem.Models.Item;
import org.university.electronic.suppliesmanagementsystem.Models.Manager;
import org.university.electronic.suppliesmanagementsystem.Models.Supplier;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import java.time.LocalDate;
import java.time.LocalDateTime;
import javafx.geometry.Insets;

public class ManagerController {

    private final Manager manager;

    public ManagerController(Manager manager) {
        this.manager = manager;
    }

    public void start(Stage primaryStage) {
        VBox layout = new VBox(15);

        Button addItemButton = new Button("Add Item");
        addItemButton.setOnAction(event -> addItem());

        Button modifyItemButton = new Button("Modify Item");
        modifyItemButton.setOnAction(event -> modifyItem());

        Button restockItemButton = new Button("Restock Item");
        restockItemButton.setOnAction(event -> restockItem());

        Button lowStockButton = new Button("View Low Stock Alerts");
        lowStockButton.setOnAction(event -> viewLowStock());

        Button generateStatisticsButton = new Button("Generate Statistics");
        generateStatisticsButton.setOnAction(event -> generateStatistics());

        Button manageSuppliersButton = new Button("Manage Suppliers");
        manageSuppliersButton.setOnAction(event -> manageSuppliers());

        // Sign Out Button
        Button signOutButton = new Button("Sign Out");
        signOutButton.setOnAction(event -> signOut(primaryStage));

        layout.getChildren().addAll(
                addItemButton,
                modifyItemButton,
                restockItemButton,
                lowStockButton,
                generateStatisticsButton,
                manageSuppliersButton,
                signOutButton
        );

        Scene scene = new Scene(layout, 400, 300);
        primaryStage.setScene(scene);
        primaryStage.setTitle("Manager Dashboard");
        primaryStage.show();
        primaryStage.setMaximized(true);
    }

    private void addItem() {
        TextInputDialog dialog = new TextInputDialog();
        dialog.setTitle("Add New Item");
        dialog.setHeaderText("Enter item details (Name, Category, Stock, Purchase Price, Selling Price):");

        dialog.showAndWait().ifPresent(input -> {
            String[] parts = input.split(",");
            if (parts.length == 5) {
                try {
                    String name = parts[0].trim();
                    String category = parts[1].trim();
                    int stock = Integer.parseInt(parts[2].trim());
                    double purchasePrice = Double.parseDouble(parts[3].trim());
                    double sellingPrice = Double.parseDouble(parts[4].trim());
                    manager.addNewItemCategory(name, category, stock, purchasePrice, sellingPrice);
                    showAlert(Alert.AlertType.INFORMATION, "Success", "Item added successfully.");
                } catch (NumberFormatException ex) {
                    showAlert(Alert.AlertType.ERROR, "Error", "Invalid numeric values for stock or prices.");
                }
            } else {
                showAlert(Alert.AlertType.ERROR, "Error", "Please enter all details in the correct format.");
            }
        });
    }

    private void modifyItem() {
        TextInputDialog dialog = new TextInputDialog();
        dialog.setTitle("Modify Item");
        dialog.setHeaderText("Enter item name to modify:");

        dialog.showAndWait().ifPresent(name -> {
            Item item = manager.getInventory().findItemByName(name.trim());
            if (item != null) {
                modifyItemDetails(item);
            } else {
                showAlert(Alert.AlertType.ERROR, "Error", "Item not found.");
            }
        });
    }

    private void modifyItemDetails(Item item) {
        TextInputDialog dialog = new TextInputDialog();
        dialog.setTitle("Modify Item Details");
        dialog.setHeaderText("Enter new stock and selling price (Stock, Selling Price):");

        dialog.showAndWait().ifPresent(input -> {
            String[] parts = input.split(",");
            if (parts.length == 2) {
                try {
                    int stock = Integer.parseInt(parts[0].trim());
                    double sellingPrice = Double.parseDouble(parts[1].trim());
                    manager.modifyItem(item.getName(), stock, sellingPrice);
                    showAlert(Alert.AlertType.INFORMATION, "Success", "Item updated successfully.");
                } catch (NumberFormatException ex) {
                    showAlert(Alert.AlertType.ERROR, "Error", "Invalid numeric values for stock or price.");
                }
            } else {
                showAlert(Alert.AlertType.ERROR, "Error", "Please enter stock and price in the correct format.");
            }
        });
    }

    private void restockItem() {
        Stage stage = new Stage();

        VBox layout = new VBox(15);
        layout.setPadding(new Insets(30));
        layout.setStyle("""
            -fx-background-color: linear-gradient(
                to bottom right,
                #020617,
                #0F172A
            );
            """);

        Label title = new Label("Search for item to restock:");
        title.setStyle("-fx-text-fill: #4DF0F8; -fx-font-size: 18px; -fx-font-weight: bold;");

        TextField searchField = new TextField();
        searchField.setPromptText("Type item name...");
        searchField.setMaxWidth(400);

        ListView<Item> suggestionsList = new ListView<>();
        suggestionsList.setMaxHeight(150);
        suggestionsList.setVisible(false);
        suggestionsList.setManaged(false);

        suggestionsList.setCellFactory(lv -> new ListCell<>() {
            @Override
            protected void updateItem(Item item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setText(null);
                } else {
                    setText(item.getName() + " | Stock: " + item.getStockLevel());
                }
            }
        });


        searchField.textProperty().addListener((obs, oldText, newText) -> {
            if (newText.isEmpty()) {
                suggestionsList.setVisible(false);
                suggestionsList.setManaged(false);
                return;
            }

            ObservableList<Item> matches = FXCollections.observableArrayList();
            for (Item item : manager.getInventory().getItems()) {
                if (item.getName().toLowerCase().contains(newText.toLowerCase())) {
                    matches.add(item);
                }
            }

            suggestionsList.setItems(matches);

            boolean show = !matches.isEmpty();
            suggestionsList.setVisible(show);
            suggestionsList.setManaged(show);
        });


        suggestionsList.setOnMouseClicked(e -> {
            Item selected = suggestionsList.getSelectionModel().getSelectedItem();
            if (selected != null) {
                stage.close();
                restockItemForm(new Stage(), selected);
            }
        });

        layout.getChildren().addAll(title, searchField, suggestionsList);

        stage.setScene(new Scene(layout, 500, 300));
        stage.setTitle("Restock Item");
        stage.show();
    }

    private void viewLowStock() {
        Stage lowStockStage = new Stage();
        VBox layout = new VBox(10);

        TableView<Item> tableView = new TableView<>();
        TableColumn<Item, String> nameColumn = new TableColumn<>("Product Name");
        nameColumn.setCellValueFactory(new PropertyValueFactory<>("name"));

        TableColumn<Item, Integer> stockColumn = new TableColumn<>("Stock");
        stockColumn.setCellValueFactory(new PropertyValueFactory<>("stockLevel"));

        ObservableList<Item> lowStockItems = FXCollections.observableArrayList(manager.getInventory().getLowStockItems(5));
        tableView.setItems(lowStockItems);

        layout.getChildren().add(tableView);

        Scene scene = new Scene(layout, 400, 300);
        lowStockStage.setScene(scene);
        lowStockStage.setTitle("Low Stock Items");
        lowStockStage.show();
    }
    private void restockItemForm(Stage stage, Item item) {
        VBox layout = new VBox(20);
        layout.setPadding(new Insets(30));
        layout.setStyle("""
            -fx-background-color: linear-gradient(
                to bottom right,
                #020617,
                #0F172A
            );
            """);

        Label title = new Label("Restock: " + item.getName());
        title.setStyle("-fx-text-fill: #4DF0F8; -fx-font-size: 20px; -fx-font-weight: bold;");

        Label currentStock = new Label("Current Stock: " + item.getStockLevel());
        currentStock.setStyle("-fx-text-fill: #E0F7FA;");

        TextField quantityField = new TextField();
        quantityField.setPromptText("Quantity to add");
        quantityField.setMaxWidth(200);

        Button restockButton = new Button("Restock");
        restockButton.setStyle("""
            -fx-background-color: linear-gradient(to right, #4CAF50, #2E7D32);
            -fx-text-fill: white;
            -fx-font-weight: bold;
            -fx-padding: 10 25;
            """);

        restockButton.setOnAction(e -> {
            try {
                int quantity = Integer.parseInt(quantityField.getText());

                if (quantity <= 0) {
                    showAlert(Alert.AlertType.ERROR, "Error", "Quantity must be positive.");
                    return;
                }

                manager.restockItem(item.getName(), quantity);

                showAlert(
                        Alert.AlertType.INFORMATION,
                        "Success",
                        "Item restocked successfully!\nNew stock: " + item.getStockLevel()
                );

                stage.close();
            } catch (NumberFormatException ex) {
                showAlert(Alert.AlertType.ERROR, "Error", "Invalid quantity.");
            }
        });

        layout.getChildren().addAll(title, currentStock, quantityField, restockButton);

        stage.setScene(new Scene(layout, 400, 250));
        stage.setTitle("Restock Item");
        stage.show();
    }

    private void generateStatistics() {
        LocalDateTime startDate = LocalDate.now().minusMonths(1).atStartOfDay();
        LocalDateTime endDate = LocalDate.now().atTime(23, 59, 59);
        manager.generateStatistics(startDate, endDate);
        showAlert(Alert.AlertType.INFORMATION, "Statistics Generated", "The statistics have been generated.");
    }

    private void manageSuppliers() {
        Stage supplierStage = new Stage();
        VBox layout = new VBox(10);

        TextField nameField = new TextField();
        nameField.setPromptText("Supplier Name");

        TextField contactField = new TextField();
        contactField.setPromptText("Contact Information");

        TextField productField = new TextField();
        productField.setPromptText("Products (comma-separated)");

        Button addSupplierButton = new Button("Add Supplier");
        addSupplierButton.setOnAction(event -> {
            String name = nameField.getText().trim();
            String contact = contactField.getText().trim();
            String[] products = productField.getText().split(",");
            if (!name.isEmpty() && !contact.isEmpty()) {
                Supplier supplier = new Supplier(name, contact);
                for (String product : products) {
                    supplier.addProduct(product.trim());
                }
                manager.addSupplier(supplier);
                showAlert(Alert.AlertType.INFORMATION, "Success", "Supplier added successfully.");
            } else {
                showAlert(Alert.AlertType.ERROR, "Error", "Name and contact information are required.");
            }
        });

        layout.getChildren().addAll(new Label("Manage Suppliers"), nameField, contactField, productField, addSupplierButton);

        Scene scene = new Scene(layout, 400, 300);
        supplierStage.setScene(scene);
        supplierStage.setTitle("Manage Suppliers");
        supplierStage.show();
    }

    private void signOut(Stage primaryStage) {
        Alert alert = new Alert(Alert.AlertType.CONFIRMATION);
        alert.setTitle("Sign Out Confirmation");
        alert.setHeaderText("Are you sure you want to sign out?");
        alert.setContentText("You will be redirected to the login screen.");

        alert.showAndWait().ifPresent(response -> {
            if (response == ButtonType.OK) {
                primaryStage.close();
                openLoginScreen();
            }
        });
    }

    private void openLoginScreen() {
        Stage loginStage = new Stage();
        loginStage.setTitle("Login");
        loginStage.show();
    }

    private void showAlert(Alert.AlertType type, String title, String message) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setContentText(message);
        alert.showAndWait();
    }
}
