package org.university.electronic.suppliesmanagementsystem.Views;


import org.university.electronic.suppliesmanagementsystem.Models.Bill;
import org.university.electronic.suppliesmanagementsystem.Models.BillManager;
import org.university.electronic.suppliesmanagementsystem.Models.Inventory;
import org.university.electronic.suppliesmanagementsystem.Models.Item;

import org.university.electronic.suppliesmanagementsystem.Controllers.LoginController;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.*;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.stage.Screen;
import javafx.stage.Stage;

import java.time.LocalDate;
import java.util.Optional;


public class CashierDashboard {

    private BillManager billManager;
    public Inventory inventory;
    private String username;
    private VBox centerContent;

    public CashierDashboard(Inventory inventory, String username) {
        this.billManager = new BillManager();
        this.inventory = inventory;
        this.username = username;
    }

    public void showCashierDashboard(Stage stage) {

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

/* ================= BUTTONS ================= */
Button createBillButton = new Button("Create Bill");
Button viewBillsButton = new Button("View Bills");
Button signOutButton = new Button("Sign Out");

stylePrimaryButton(createBillButton);
stylePrimaryButton(viewBillsButton);

        signOutButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #C1121F, #9B2226);
                -fx-text-fill: white;
                -fx-font-size: 14px;
                -fx-font-weight: bold;
                -fx-background-radius: 12;
                -fx-padding: 10 20;
                """);
        signOutButton.setMaxWidth(260);


ListView<String> billsListView = new ListView<>();
        billsListView.setPrefHeight(250);
        billsListView.setStyle("""
                -fx-background-color: #020617;
                -fx-control-inner-background: #020617;
                -fx-text-fill: #E0F7FA;
                -fx-border-color: #00B4D8;
                -fx-border-radius: 10;
                """);


        createBillButton.setOnAction(e -> createMultiItemBill());
        viewBillsButton.setOnAction(e -> viewBillsInDateRange(billsListView));

        signOutButton.setOnAction(e -> {
LoginController loginController = new LoginController();
            try {
                    loginController.start(stage);
            } catch (Exception ex) {
        ex.printStackTrace();
            }
                    });

Region spacer = new Region();
        VBox.setVgrow(spacer, Priority.ALWAYS);

VBox sidebar = new VBox(25, menuTitle, viewBillsButton, createBillButton, spacer, signOutButton);
        sidebar.setPadding(new Insets(30));
        sidebar.setPrefWidth(250);
        sidebar.setStyle("-fx-background-color: rgba(2,6,23,0.95);");

/* ================= HEADER ================= */
Label titleLabel = new Label("Cashier Dashboard");
        titleLabel.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 26px;
            -fx-font-weight: bold;
        """);

HBox header = new HBox(20, titleLabel);
        header.setAlignment(Pos.CENTER_LEFT);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2,6,23,0.9);");


centerContent = new VBox(25);
        centerContent.setPadding(new Insets(25));
        VBox.setVgrow(billsListView, Priority.ALWAYS);

/* ================= ADD ALL ================= */
        root.setLeft(sidebar);
        root.setTop(header);
        root.setCenter(centerContent);

        viewBillsInDateRange(billsListView);

        Scene scene = new Scene(root);
        stage.setScene(scene);
        stage.setTitle("Cashier Dashboard");
        stage.setWidth(Screen.getPrimary().getBounds().getWidth());
        stage.setHeight(Screen.getPrimary().getBounds().getHeight());
        stage.show();
    }

/* ================= BUTTON STYLE ================= */
private void stylePrimaryButton(Button button) {
    button.setStyle("""
                -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                -fx-text-fill: white;
                -fx-font-size: 14px;
                -fx-font-weight: bold;
                -fx-background-radius: 12;
                -fx-padding: 10 20;
                -fx-cursor: hand;
                """);
    button.setMaxWidth(240);
}


private void createMultiItemBill() {

    final Bill[] bill = new Bill[] {
            new Bill(billManager.getNextBillId())
    };

    // ===== Form controls =====
    ComboBox<Item> itemComboBox = new ComboBox<>();
    itemComboBox.getItems().addAll(inventory.getItems());
    itemComboBox.setPromptText("Select Item");
    itemComboBox.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 5 10;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

    TextField quantityField = new TextField();
    quantityField.setPromptText("Quantity");
    quantityField.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 5 10;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

    Button addItemButton = new Button("Add Item");
    stylePrimaryButton(addItemButton);

    ListView<String> billItemsView = new ListView<>();
    billItemsView.setStyle("""
                -fx-background-color: #020617;
                -fx-control-inner-background: #020617;
                -fx-text-fill: #E0F7FA;
                -fx-border-color: #00B4D8;
                -fx-border-radius: 10;
                """);

    Label totalLabel = new Label("Total: $0.00");
    totalLabel.setStyle("-fx-font-size: 16px; -fx-font-weight: bold; -fx-text-fill:white;");

    Button finalizeBillButton = new Button("Finalize Bill");
    stylePrimaryButton(finalizeBillButton);


    addItemButton.setOnAction(e -> {
        Item selectedItem = itemComboBox.getValue();
        String quantityText = quantityField.getText();

        if (selectedItem == null || quantityText.isEmpty()) {
            showAlert(Alert.AlertType.ERROR, "Missing Data",
                    "Please select an item and enter quantity.");
            return;
        }

        try {
            int quantity = Integer.parseInt(quantityText);

            if (quantity <= 0 || quantity > selectedItem.getStockLevel()) {
                showAlert(Alert.AlertType.ERROR, "Invalid Quantity",
                        "Quantity must be between 1 and " + selectedItem.getStockLevel());
                return;
            }

            selectedItem.setStockLevel(selectedItem.getStockLevel() - quantity);
            bill[0].addItem(selectedItem, quantity);
            inventory.saveInventory();

            billItemsView.getItems().add(
                    selectedItem.getName() + " x" + quantity + " — $" +
                            String.format("%.2f", selectedItem.getSellingPrice() * quantity)
            );

            totalLabel.setText("Total: $" + String.format("%.2f", bill[0].getTotalAmount()));

            quantityField.clear();
            itemComboBox.setValue(null);

        } catch (NumberFormatException ex) {
            showAlert(Alert.AlertType.ERROR, "Invalid Input",
                    "Quantity must be a valid number.");
        }
    });


    finalizeBillButton.setOnAction(e -> {
        if (bill[0].getBillItems().isEmpty()) {
            showAlert(Alert.AlertType.ERROR, "Empty Bill",
                    "Please add at least one item.");
            return;
        }

        billManager.addBill(bill[0]);
        billManager.saveBillToFile(bill[0]);

        showAlert(Alert.AlertType.INFORMATION, "Bill Created",
                "Bill created successfully!\nTotal: $" +
                        String.format("%.2f", bill[0].getTotalAmount()));

        billItemsView.getItems().clear();
        totalLabel.setText("Total: $0.00");
        itemComboBox.setValue(null);
        quantityField.clear();


        new Bill(billManager.getNextBillId());

    });

    // ===== Layout =====
    HBox inputRow = new HBox(15, itemComboBox, quantityField);
    inputRow.setAlignment(Pos.CENTER_LEFT);

    VBox billForm = new VBox(20,
            inputRow,
            addItemButton,
            billItemsView,
            totalLabel,
            finalizeBillButton
    );
    VBox.setVgrow(billItemsView, Priority.ALWAYS);

    centerContent.getChildren().setAll(
            createCard("Create Multi-Item Bill", billForm)
    );
}

private void viewBillsInDateRange(ListView<String> billsListView) {
    DatePicker startDatePicker = new DatePicker();
    DatePicker endDatePicker = new DatePicker();

    Label start = new Label("Start Date:");
    start.setStyle("-fx-text-fill:white;-fx-font-size:16;");
    Label end = new Label("End Date:");
    end.setStyle("-fx-text-fill:white;-fx-font-size:16;");

    VBox datePickerLayout = new VBox(10,
            start, startDatePicker,
            end, endDatePicker
    );

    startDatePicker.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 5 10;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);
    endDatePicker.setStyle("""
            -fx-background-color: rgba(255,255,255,0.95);
            -fx-background-radius: 10;
            -fx-padding: 5 10;
            -fx-font-size: 14px;
            -fx-border-color: #00B4D8;
            -fx-border-radius: 10;
            """);

    Button filterButton = new Button("Filter Bills");
    stylePrimaryButton(filterButton);

    filterButton.setOnAction(e -> {
        LocalDate startDate = startDatePicker.getValue();
        LocalDate endDate = endDatePicker.getValue();

        if (startDate == null || endDate == null || startDate.isAfter(endDate)) {
            showAlert(Alert.AlertType.ERROR, "Invalid Date Range",
                    "Please select a valid date range.");
            return;
        }

        billsListView.getItems().clear();

        for (Bill bill : billManager.getBillsWithinDateRange(startDate, endDate)) {
            billsListView.getItems().add(bill.toString());
        }
    });

    centerContent.getChildren().setAll(createCard("Select Date Range", datePickerLayout, filterButton, billsListView));
}

private void showAlert(Alert.AlertType type, String title, String message) {
    Alert alert = new Alert(type);
    alert.setTitle(title);
    alert.setContentText(message);
    alert.showAndWait();
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
}


