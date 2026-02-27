package org.university.electronic.suppliesmanagementsystem.Controllers;

import java.util.Optional;

import javafx.beans.property.SimpleDoubleProperty;
import javafx.beans.property.SimpleIntegerProperty;
import javafx.beans.property.SimpleStringProperty;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

// MODELS
import org.university.electronic.suppliesmanagementsystem.Models.Bill;
import org.university.electronic.suppliesmanagementsystem.Models.Cashier;
import org.university.electronic.suppliesmanagementsystem.Models.Inventory;
import org.university.electronic.suppliesmanagementsystem.Models.Item;

// CONTROLLERS
import org.university.electronic.suppliesmanagementsystem.Controllers.LoginController;

public class CashierController {

    private final Cashier cashier;
    private final Inventory inventory;
    public final ObservableList<Item> availableItems;
    public TableView<Item> itemsTable;

    public CashierController(Cashier cashier, Inventory inventory) {
        this.cashier = cashier;
        this.inventory = inventory;
        this.availableItems = FXCollections.observableArrayList(inventory.getItems());
    }

    public void start(Stage primaryStage) {
        //creates a table to display available items
        itemsTable = new TableView<>(availableItems);

        //defines columns for the table
        TableColumn<Item, String> nameColumn = new TableColumn<>("Item Name");
        nameColumn.setCellValueFactory(cellData -> new SimpleStringProperty(cellData.getValue().getName()));

        TableColumn<Item, Double> priceColumn = new TableColumn<>("Price");
        priceColumn.setCellValueFactory(cellData -> new SimpleDoubleProperty(cellData.getValue().getSellingPrice()).asObject());

        TableColumn<Item, Integer> stockColumn = new TableColumn<>("Stock");
        stockColumn.setCellValueFactory(cellData -> new SimpleIntegerProperty(cellData.getValue().getStockLevel()).asObject());

     

        //creates a button to generate bills
        Button createBillButton = new Button("Create Bill");
        createBillButton.setOnAction(event -> createBill());

        //creates a Sign Out button
        Button signOutButton = new Button("Sign Out");
        signOutButton.setOnAction(event -> signOut(primaryStage));

        //layout for the Cashier dashboard
        VBox layout = new VBox(10, itemsTable, createBillButton, signOutButton);
        layout.setStyle("-fx-padding: 10; -fx-alignment: center;");

        //sets the scene and show the dashboard
        Scene scene = new Scene(layout, 600, 400);
        primaryStage.setScene(scene);
        primaryStage.setTitle("Cashier Dashboard");
        primaryStage.show();
        primaryStage.setMaximized(true);
    }

    public void createBill() {
        Bill bill = cashier.createBill();

        while (true) {
            Item selectedItem = itemsTable.getSelectionModel().getSelectedItem();

            if (selectedItem == null) {
                showAlert(Alert.AlertType.ERROR, "No Item Selected", "Please select an item to create a bill.");
                return;
            }

            TextInputDialog dialog = new TextInputDialog();
            dialog.setTitle("Enter Quantity");
            dialog.setHeaderText("Enter the quantity for " + selectedItem.getName());
            dialog.setContentText("Quantity:");

            Optional<String> result = dialog.showAndWait();

            if (result.isPresent()) {
                try {
                    int quantity = Integer.parseInt(result.get());
                    if (quantity <= 0 || quantity > selectedItem.getStockLevel()) {
                        showAlert(Alert.AlertType.ERROR, "Invalid Quantity",
                                "Please enter a valid quantity between 1 and " + selectedItem.getStockLevel());
                        return;
                    }

                    bill.addItem(selectedItem, quantity);
                    selectedItem.setStockLevel(selectedItem.getStockLevel() - quantity);
                    inventory.saveInventory();

                } catch (NumberFormatException e) {
                    showAlert(Alert.AlertType.ERROR, "Invalid Input", "Please enter a valid number for quantity.");
                }
            } else {
                break;
            }
        }

        cashier.saveBillToFile(bill);
        showAlert(Alert.AlertType.INFORMATION, "Bill Created", "Bill created successfully!");
    }

    private void showAlert(Alert.AlertType alertType, String title, String message) {
        Alert alert = new Alert(alertType);
        alert.setTitle(title);
        alert.setContentText(message);
        alert.showAndWait();
    }

    //method to handle sign-out
    public void signOut(Stage primaryStage) {
        //closes the current cashier dashboard
        primaryStage.close();

        //redirects to Login screen
        LoginController loginController = new LoginController();
        loginController.start(new Stage());
    }
}
