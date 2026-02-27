package org.university.electronic.suppliesmanagementsystem.Views;

import org.university.electronic.suppliesmanagementsystem.Models.Manager;
import org.university.electronic.suppliesmanagementsystem.Models.Item;
import org.university.electronic.suppliesmanagementsystem.Models.Supplier;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.stage.Modality;
import javafx.stage.Screen;
import javafx.stage.Stage;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.HBox;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;


import java.time.LocalDate;

public class ManagerDashboard {

    private final Manager manager;

    private Label totalItemsValueLabel;
    private Label lowStockValueLabel;
    private Label suppliersValueLabel;


    public ManagerDashboard(Manager manager) {
        this.manager = manager;
    }

    public void showManagerDashboard(Stage stage) {

        BorderPane root = new BorderPane();
        root.setPadding(new Insets(0));
        root.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        /* ================= LEFT SIDEBAR ================= */
        Pane sidebar = createSidebar();
        root.setLeft(sidebar);


        /* ================= HEADER ================= */
        Label titleLabel = new Label("Manager Dashboard");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 26px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        root.setTop(header);


        GridPane buttonGrid = createButtonGrid();
        buttonGrid.setAlignment(Pos.CENTER);

        ScrollPane scrollPane = new ScrollPane(buttonGrid);

        scrollPane.setStyle("-fx-background-color: transparent; -fx-background: transparent;");
        scrollPane.setHbarPolicy(ScrollPane.ScrollBarPolicy.NEVER);
        scrollPane.setVbarPolicy(ScrollPane.ScrollBarPolicy.NEVER);

        VBox mainCenter = new VBox(scrollPane);
        VBox.setVgrow(scrollPane, Priority.ALWAYS);
        mainCenter.setAlignment(Pos.CENTER);
        mainCenter.setStyle(" -fx-background-color: transparent");

        root.setCenter(mainCenter);

        Scene scene = new Scene(root, Screen.getPrimary().getBounds().getWidth(),
                Screen.getPrimary().getBounds().getHeight());
        stage.setScene(scene);
        stage.setTitle("Manager Dashboard");
        stage.show();
    }

    private Pane createSidebar() {

        BorderPane sidebar = new BorderPane();
        sidebar.setPrefWidth(250);
        sidebar.setStyle("-fx-background-color: rgba(2, 6, 23, 0.95);");
        sidebar.setPadding(new Insets(30, 20, 30, 20)); // padding i përgjithshëm

        /* ===== TOP CONTENT ===== */
        VBox topContent = new VBox(20);

        Label menuTitle = new Label("Dashboard");
        menuTitle.setStyle("""
        -fx-text-fill: #4DF0F8;
        -fx-font-size: 24px;
        -fx-font-weight: bold;
        """);

        totalItemsValueLabel = new Label();
        lowStockValueLabel = new Label();
        suppliersValueLabel = new Label();

        VBox statsContainer = new VBox(15);
        statsContainer.getChildren().addAll(
                createSidebarStatCard("Total Items", totalItemsValueLabel),
                createSidebarStatCard("Low Stock Items", lowStockValueLabel),
                createSidebarStatCard("Suppliers", suppliersValueLabel)
        );


        refreshSidebarStats();

        topContent.getChildren().addAll(menuTitle, statsContainer);

        Region spacer = new Region();
        VBox.setVgrow(spacer, Priority.ALWAYS);

        /* ===== SIGN OUT BUTTON ===== */  Button signOutButton = createMenuButton("Sign Out");
        signOutButton.setStyle("""
        -fx-background-color: linear-gradient(to right, #C1121F, #9B2226);
        -fx-text-fill: white;
        -fx-font-weight: bold;
        -fx-background-radius: 12;
        -fx-padding: 10 15;
        """);

        signOutButton.setOnAction(e ->
                signOut((Stage) signOutButton.getScene().getWindow())
        );


        VBox bottomBox = new VBox(signOutButton);
        bottomBox.setAlignment(Pos.BOTTOM_CENTER);
        bottomBox.setPadding(new Insets(0, 0, 25, 0));


        sidebar.setTop(topContent);
        sidebar.setCenter(spacer);
        sidebar.setBottom(bottomBox);

        return sidebar;
    }

    private VBox createSidebarStatCard(String title, Label valueLabel) {
        VBox card = new VBox(8);
        card.setPadding(new Insets(15));
        card.setStyle("""
            -fx-background-color: rgba(0, 180, 216, 0.15);
            -fx-background-radius: 12;
            -fx-border-color: rgba(77, 240, 248, 0.3);
            -fx-border-radius: 12;
            -fx-border-width: 1;
            """);

        Label titleLabel = new Label(title);
        titleLabel.setStyle("""
            -fx-text-fill: #E0F7FA;
            -fx-font-size: 14px;
            -fx-font-weight: bold;
            """);

        valueLabel.setStyle("""
            -fx-text-fill: #4DF0F8;
            -fx-font-size: 28px;
            -fx-font-weight: bold;
            """);

        HBox valueContainer = new HBox(valueLabel);
        valueContainer.setAlignment(Pos.CENTER_RIGHT);

        card.getChildren().addAll(titleLabel, valueContainer);
        return card;
    }

    private GridPane createButtonGrid() {
        GridPane buttonGrid = new GridPane();
        buttonGrid.setHgap(30);
        buttonGrid.setVgap(25);
        buttonGrid.setPadding(new Insets(90, 100, 40, 100));
        buttonGrid.setAlignment(Pos.CENTER);

        // - Add New Item
        Button addItemButton = createLargeButton("Add New Item");
        addItemButton.setOnAction(e -> addNewItem(new Stage()));
        buttonGrid.add(addItemButton, 0, 0);

        // - Modify Existing Item
        Button modifyItemButton = createLargeButton("Modify Existing Item");
        modifyItemButton.setOnAction(e -> modifyItem(new Stage()));
        buttonGrid.add(modifyItemButton, 1, 0);

        //  - Restock Item
        Button restockItemButton = createLargeButton("Restock Item");
        restockItemButton.setOnAction(e -> restockItem(new Stage()));
        buttonGrid.add(restockItemButton, 2, 0);

        //  - View Low Stock Alerts
        Button viewLowStockButton = createLargeButton("View Low Stock Alerts");
        viewLowStockButton.setOnAction(e -> viewLowStock());
        buttonGrid.add(viewLowStockButton, 0, 1);

        //  - Generate Sales Statistics
        Button generateStatisticsButton = createLargeButton("Generate Sales Statistics");
        generateStatisticsButton.setOnAction(e -> generateStatistics());
        buttonGrid.add(generateStatisticsButton, 1, 1);

        // - Manage Suppliers
        Button manageSuppliersButton = createLargeButton("Manage Suppliers");
        manageSuppliersButton.setOnAction(e -> manageSuppliers(new Stage()));
        buttonGrid.add(manageSuppliersButton, 2, 1);

        //  - View Products
        Button viewProductsButton = createLargeButton("View Products");
        viewProductsButton.setOnAction(e -> showProductDisplay(new Stage()));
        buttonGrid.add(viewProductsButton, 0, 2, 3, 1); // Span 3 columns

        //  - View Suppliers
        Button viewSuppliersButton = createLargeButton("View Suppliers");
        viewSuppliersButton.setOnAction(e -> viewSuppliers(new Stage()));
        buttonGrid.add(viewSuppliersButton, 1, 2);


        for (javafx.scene.Node node : buttonGrid.getChildren()) {
            if (node instanceof Button) {
                ((Button) node).setPrefWidth(250);
                ((Button) node).setPrefHeight(80);
            }
        }

        return buttonGrid;
    }

    private Button createLargeButton(String text) {
        Button button = new Button(text);
        button.setStyle("""
                -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                -fx-text-fill: white;
                -fx-font-size: 16px;
                -fx-font-weight: bold;
                -fx-background-radius: 15;
                -fx-padding: 20;
                -fx-effect: dropshadow(gaussian, rgba(0,0,0,0.3), 10, 0.5, 0, 3);
                """);

        button.setOnMouseEntered(e -> {
            button.setStyle("""
                    -fx-background-color: linear-gradient(to right, #0096C7, #005F8A);
                    -fx-text-fill: white;
                    -fx-font-size: 16px;
                    -fx-font-weight: bold;
                    -fx-background-radius: 15;
                    -fx-padding: 20;
                    -fx-effect: dropshadow(gaussian, rgba(0,0,0,0.4), 12, 0.6, 0, 4);
                    -fx-cursor: hand;
                    """);
        });

        button.setOnMouseExited(e -> {
            button.setStyle("""
                    -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                    -fx-text-fill: white;
                    -fx-font-size: 16px;
                    -fx-font-weight: bold;
                    -fx-background-radius: 15;
                    -fx-padding: 20;
                    -fx-effect: dropshadow(gaussian, rgba(0,0,0,0.3), 10, 0.5, 0, 3);
                    """);
        });

        return button;
    }

    private Button createMenuButton(String text) {
        Button button = new Button(text);
        button.setMaxWidth(Double.MAX_VALUE);
        button.setStyle("""
                -fx-background-color: linear-gradient(to right, #00B4D8, #0077B6);
                -fx-text-fill: white;
                -fx-font-size: 14px;
                -fx-font-weight: bold;
                -fx-background-radius: 12;
                -fx-padding: 20 15;
                """);
        button.setAlignment(Pos.CENTER);
        button.setMaxWidth(200);
        return button;
    }



    private void addNewItem(Stage stage) {
        Stage formStage = new Stage();
        formStage.initOwner(stage);
        formStage.initModality(Modality.WINDOW_MODAL);

        BorderPane formRoot = new BorderPane();
        formRoot.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        Label titleLabel = new Label("Add New Item");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        VBox formLayout = new VBox(15);
        formLayout.setPadding(new Insets(30));
        formLayout.setStyle("-fx-background-color: transparent;");
        formLayout.setAlignment(Pos.CENTER);

        TextField nameField = createStyledTextField("Item Name");
        TextField categoryField = createStyledTextField("Category");
        TextField stockField = createStyledTextField("Stock");
        TextField purchasePriceField = createStyledTextField("Purchase Price");
        TextField sellingPriceField = createStyledTextField("Selling Price");

        Button submitButton = createMenuButton("Add Item");
        submitButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #4CAF50, #2E7D32);
                -fx-text-fill: white;
                -fx-font-weight: bold;
                """);

        submitButton.setOnAction(e -> {
            try {
                String name = nameField.getText();
                String category = categoryField.getText();
                int stock = Integer.parseInt(stockField.getText());
                double purchasePrice = Double.parseDouble(purchasePriceField.getText());
                double sellingPrice = Double.parseDouble(sellingPriceField.getText());

                if (name.isEmpty() || category.isEmpty()) {
                    showStyledAlert("Error", "Please fill in all fields.");
                    return;
                }

                manager.addNewItemCategory(name, category, stock, purchasePrice, sellingPrice);
                refreshSidebarStats();

                showStyledAlert("Success", "Item added successfully.");
                formStage.close();
            } catch (NumberFormatException ex) {
                showStyledAlert("Error", "Please enter valid numeric values for stock and prices.");
            }
        });

        formLayout.getChildren().addAll(
                nameField, categoryField, stockField,
                purchasePriceField, sellingPriceField, submitButton
        );

        formRoot.setTop(header);
        formRoot.setCenter(formLayout);

        Scene formScene = new Scene(formRoot, 1000,
                Screen.getPrimary().getBounds().getHeight());
        formStage.setScene(formScene);
        formStage.setTitle("Add New Item");

        formStage.show();

    }

    private void modifyItem(Stage stage) {
        Stage formStage = new Stage();

        BorderPane formRoot = new BorderPane();
        formRoot.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);


        Label titleLabel = new Label("Modify Item");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        VBox formLayout = new VBox(10);
        formLayout.setPadding(new Insets(30));
        formLayout.setStyle("-fx-background-color: transparent;");

        TextField nameField = createStyledTextField("Enter Item Name to Modify");

        ListView<Item> suggestionsList = new ListView<>();
        suggestionsList.setMaxHeight(120);
        suggestionsList.setVisible(false);
        suggestionsList.setStyle("""
                -fx-background-color: rgba(255,255,255,0.95);
                -fx-control-inner-background: rgba(255,255,255,0.95);
                """);

        nameField.textProperty().addListener((obs, oldText, newText) -> {
            if (newText.isEmpty()) {
                suggestionsList.setVisible(false);
                return;
            }

            ObservableList<Item> matches = FXCollections.observableArrayList();
            for (Item item : manager.getInventory().getItems()) {
                if (item.getName().toLowerCase().startsWith(newText.toLowerCase())) {
                    matches.add(item);
                }
            }

            suggestionsList.setItems(matches);
            suggestionsList.setVisible(!matches.isEmpty());
        });

        suggestionsList.setOnMouseClicked(e -> {
            Item selected = suggestionsList.getSelectionModel().getSelectedItem();
            if (selected != null) {
                formStage.close();
                modifyItemForm(new Stage(), selected);
            }
        });
        Label title = new Label("Search for item to modify:");
        title.setStyle("-fx-text-fill:white");

        formLayout.getChildren().addAll(
                title,
                nameField,
                suggestionsList
        );
        formLayout.setAlignment(Pos.CENTER);

        formRoot.setTop(header);
        formRoot.setCenter(formLayout);

        Scene scene = new Scene(formRoot,1000,
                Screen.getPrimary().getBounds().getHeight());
        formStage.setScene(scene);
        formStage.setTitle("Modify Item");
        formStage.show();
    }

    private void modifyItemForm(Stage stage, Item item) {
        BorderPane formRoot = new BorderPane();
        formRoot.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        Label titleLabel = new Label("Modify: " + item.getName());
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        VBox formLayout = new VBox(15);
        formLayout.setPadding(new Insets(30));
        formLayout.setStyle("-fx-background-color: transparent;");

        Label itemInfo = new Label("Item: " + item.getName() + " | Category: " + item.getCategory());
        itemInfo.setStyle("-fx-text-fill: #E0F7FA; -fx-font-size: 14px;");

        TextField stockField = createStyledTextField("Stock");
        stockField.setText(String.valueOf(item.getStockLevel()));

        TextField sellingPriceField = createStyledTextField("Selling Price");
        sellingPriceField.setText(String.valueOf(item.getSellingPrice()));

        Button submitButton = createMenuButton("Update Item");
        submitButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #4CAF50, #2E7D32);
                -fx-text-fill: white;
                -fx-font-weight: bold;
                """);

        submitButton.setOnAction(e -> {
            try {
                int stock = Integer.parseInt(stockField.getText());
                double sellingPrice = Double.parseDouble(sellingPriceField.getText());

                if (stock < 0 || sellingPrice < 0) {
                    showStyledAlert("Error", "Values cannot be negative.");
                    return;
                }

                item.setStockLevel(stock);
                item.setSellingPrice(sellingPrice);
                manager.getInventory().saveInventory();
                refreshSidebarStats();
                showStyledAlert("Success", "Item updated successfully.");
                stage.close();
            } catch (NumberFormatException ex) {
                showStyledAlert("Error", "Please enter valid numeric values.");
            }
        });

        formLayout.getChildren().addAll(itemInfo, stockField, sellingPriceField, submitButton);

        formRoot.setTop(header);
        formRoot.setCenter(formLayout);

        Scene formScene = new Scene(formRoot, 400, 300);
        stage.setScene(formScene);
        stage.setTitle("Modify Item");
        stage.show();
    }

    private void restockItem(Stage stage) {
        Stage restockStage = new Stage();

        VBox layout = new VBox(15);
        layout.setPadding(new Insets(30));
        layout.setStyle("""
        -fx-background-color: linear-gradient(
            to bottom right,
            #020617,
            #0F172A
        );
        """);

        Label title = new Label("Search item to restock:");
        title.setStyle("-fx-text-fill: #4DF0F8; -fx-font-size: 18px; -fx-font-weight: bold;");

        TextField searchField = createStyledTextField("Type item name...");
        ListView<Item> listView = new ListView<>();
        listView.setMaxHeight(150);
        listView.setVisible(false);
        listView.setManaged(false);

        listView.setCellFactory(lv -> new ListCell<>() {
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
                listView.setVisible(false);
                listView.setManaged(false);
                return;
            }

            ObservableList<Item> matches = FXCollections.observableArrayList();
            for (Item item : manager.getInventory().getItems()) {
                if (item.getName().toLowerCase().contains(newText.toLowerCase())) {
                    matches.add(item);
                }
            }

            listView.setItems(matches);
            boolean show = !matches.isEmpty();
            listView.setVisible(show);
            listView.setManaged(show);
        });

        listView.setOnMouseClicked(e -> {
            Item selected = listView.getSelectionModel().getSelectedItem();
            if (selected != null) {
                restockStage.close();
                openRestockQuantityForm(selected);
            }
        });

        layout.getChildren().addAll(title, searchField, listView);

        restockStage.setScene(new Scene(layout, 500, 300));
        restockStage.setTitle("Restock Item");
        restockStage.show();
    }
    private void openRestockQuantityForm(Item item) {
        Stage stage = new Stage();

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

        Label stock = new Label("Current Stock: " + item.getStockLevel());
        stock.setStyle("-fx-text-fill: #E0F7FA;");

        TextField quantityField = createStyledTextField("Quantity");

        Button restockBtn = createMenuButton("Restock");
        restockBtn.setOnAction(e -> {
            try {
                int qty = Integer.parseInt(quantityField.getText());
                if (qty <= 0) {
                    showStyledAlert("Error", "Quantity must be positive.");
                    return;
                }

                manager.restockItem(item.getName(), qty);
                refreshSidebarStats();
                showStyledAlert("Success", "New stock: " + item.getStockLevel());
                stage.close();

            } catch (NumberFormatException ex) {
                showStyledAlert("Error", "Invalid quantity.");
            }
        });

        layout.getChildren().addAll(title, stock, quantityField, restockBtn);

        stage.setScene(new Scene(layout, 400, 250));
        stage.setTitle("Restock Item");
        stage.show();
    }

    private void viewLowStock() {
        Stage stage = new Stage();

        BorderPane root = new BorderPane();
        root.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        Label titleLabel = new Label("Low Stock Alerts");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        TableView<Item> table = new TableView<>();
        table.setStyle("""
                -fx-background-color: #020617;
                -fx-control-inner-background: #020617;
                -fx-table-cell-border-color: transparent;
                -fx-selection-bar: rgba(255,107,107,0.35);
                -fx-selection-bar-non-focused: rgba(255,107,107,0.25);
                """);

        TableColumn<Item, String> nameCol = new TableColumn<>("Item Name");
        nameCol.setCellValueFactory(new PropertyValueFactory<>("name"));
        nameCol.setStyle("-fx-text-fill: #38BDF8;");

        TableColumn<Item, String> categoryCol = new TableColumn<>("Category");
        categoryCol.setCellValueFactory(new PropertyValueFactory<>("category"));
        categoryCol.setStyle("-fx-text-fill: #38BDF8;");

        TableColumn<Item, Integer> stockCol = new TableColumn<>("Stock");
        stockCol.setCellValueFactory(new PropertyValueFactory<>("stockLevel"));
        stockCol.setStyle("-fx-text-fill: #38BDF8;");

        table.getColumns().addAll(nameCol, categoryCol, stockCol);

        ObservableList<Item> lowStockItems = FXCollections.observableArrayList(
                manager.getInventory().getLowStockItems(5)
        );

        table.setItems(lowStockItems);

        VBox layout = new VBox(10, table);
        layout.setPadding(new Insets(20));
        layout.setStyle("-fx-background-color: transparent;");

        root.setTop(header);
        root.setCenter(layout);

        stage.setScene(new Scene(root, 1000, Screen.getPrimary().getBounds().getHeight()));
        stage.setTitle("Low Stock Alerts");
        stage.show();
    }

    private void generateStatistics() {
        Stage chartStage = new Stage();

        BorderPane root = new BorderPane();
        root.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        Label titleLabel = new Label("Sales Statistics");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        CategoryAxis xAxis = new CategoryAxis();
        xAxis.setLabel("Products");
        xAxis.setStyle("-fx-text-fill: white; -fx-tick-label-fill: white;");

        NumberAxis yAxis = new NumberAxis();
        yAxis.setLabel("Profit");
        yAxis.setStyle("-fx-text-fill: white; -fx-tick-label-fill: white;");

        BarChart<String, Number> barChart = new BarChart<>(xAxis, yAxis);

        Label label = new Label("Profit per Product");
        label.setStyle("-fx-text-fill:white; -fx-font-size:18px; fx-font-weight:bold; -fx-padding:10");

        barChart.setStyle("-fx-text-fill: white;");
        barChart.setLegendVisible(true);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Total Profit");

        for (Item item : manager.getInventory().getItems()) {
            double profit = (item.getSellingPrice() - item.getPurchasePrice()) * item.getStockLevel();
            series.getData().add(new XYChart.Data<>(item.getName(), profit));
        }

        barChart.getData().add(series);

        barChart.getData().forEach(data -> {
            data.getData().forEach(node -> {
                node.getNode().setStyle("-fx-bar-fill: #00B4D8;");
            });
        });

        VBox layout = new VBox(label,barChart);
        layout.setPadding(new Insets(20));
        layout.setStyle("-fx-background-color: transparent;");
        layout.setAlignment(Pos.CENTER);

        root.setTop(header);
        root.setCenter(layout);

        Scene scene = new Scene(root, 1000,
                Screen.getPrimary().getBounds().getHeight());
        chartStage.setScene(scene);
        chartStage.setTitle("Sales Statistics");
        chartStage.show();
    }

    private void manageSuppliers(Stage stage) {
        Stage supplierStage = new Stage();

        BorderPane root = new BorderPane();
        root.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        Label titleLabel = new Label("Manage Suppliers");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        VBox formLayout = new VBox(15);
        formLayout.setPadding(new Insets(30));
        formLayout.setStyle("-fx-background-color: transparent;");

        TextField nameField = createStyledTextField("Supplier Name");
        TextField contactField = createStyledTextField("Contact Information");
        TextField productField = createStyledTextField("Products (comma-separated)");

        Button addSupplierButton = createMenuButton("Add Supplier");
        addSupplierButton.setStyle("""
                -fx-background-color: linear-gradient(to right, #9C27B0, #6A1B9A);
                -fx-text-fill: white;
                -fx-font-weight: bold;
                """);

        addSupplierButton.setOnAction(e -> {
            String name = nameField.getText().trim();
            String contact = contactField.getText().trim();
            String[] products = productField.getText().split(",");

            if (!name.isEmpty() && !contact.isEmpty()) {
                Supplier supplier = new Supplier(name, contact);
                for (String product : products) {
                    supplier.addProduct(product.trim());
                }
                manager.addSupplier(supplier);
                refreshSidebarStats();
                showStyledAlert("Success", "Supplier added successfully.");
                nameField.clear();
                contactField.clear();
                productField.clear();
            } else {
                showStyledAlert("Error", "Name and contact information are required.");
            }
        });

        formLayout.getChildren().addAll(nameField, contactField, productField, addSupplierButton);
        formLayout.setAlignment(Pos.CENTER);

        root.setTop(header);
        root.setCenter(formLayout);

        Scene scene = new Scene(root, 1000,
                Screen.getPrimary().getBounds().getHeight());
        supplierStage.setScene(scene);
        supplierStage.setTitle("Manage Suppliers");
        supplierStage.show();
    }
    private void viewSuppliers(Stage stage) {

        BorderPane root = new BorderPane();
        root.setStyle("""
        -fx-background-color: linear-gradient(
            to bottom right,
            #020617,
            #0F172A
        );
    """);

        Label titleLabel = new Label("Suppliers List");
        titleLabel.setStyle("""
        -fx-text-fill: #4DF0F8;
        -fx-font-size: 24px;
        -fx-font-weight: bold;
    """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2,6,23,0.9);");

        TableView<Supplier> table = new TableView<>();
        table.setStyle("""
        -fx-background-color: #020617;
        -fx-control-inner-background: #020617;
    """);

        TableColumn<Supplier, String> nameCol =
                new TableColumn<>("Name");
        nameCol.setCellValueFactory(
                new PropertyValueFactory<>("name")
        );

        TableColumn<Supplier, String> contactCol =
                new TableColumn<>("Contact");
        contactCol.setCellValueFactory(
                new PropertyValueFactory<>("contactInfo")
        );

        TableColumn<Supplier, String> productsCol =
                new TableColumn<>("Products");
        productsCol.setCellValueFactory(cell ->
                new javafx.beans.property.SimpleStringProperty(
                        String.join(", ", cell.getValue().getProducts())
                )
        );

        table.getColumns().addAll(nameCol, contactCol, productsCol);

        ObservableList<Supplier> suppliers =
                FXCollections.observableArrayList(manager.getSuppliers());

        table.setItems(suppliers);

        VBox center = new VBox(table);
        center.setPadding(new Insets(20));

        root.setTop(header);
        root.setCenter(center);

        stage.setScene(new Scene(root, 900, 500));
        stage.setTitle("View Suppliers");
        stage.show();
    }

    private void showProductDisplay(Stage stage) {
        Stage productStage = new Stage();

        BorderPane root = new BorderPane();
        root.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                """);

        Label titleLabel = new Label("Product Inventory");
        titleLabel.setStyle("""
                -fx-text-fill: #4DF0F8;
                -fx-font-size: 24px;
                -fx-font-weight: bold;
                """);

        HBox header = new HBox(titleLabel);
        header.setAlignment(Pos.CENTER);
        header.setPadding(new Insets(20));
        header.setStyle("-fx-background-color: rgba(2, 6, 23, 0.9);");

        TableView<Item> productTable = new TableView<>();
        productTable.setStyle("""
                -fx-background-color: #020617;
                -fx-control-inner-background: #020617;
                -fx-table-cell-border-color: transparent;
                -fx-selection-bar: rgba(0,180,216,0.35);
                -fx-selection-bar-non-focused: rgba(0,180,216,0.25);
                """);

        TableColumn<Item, String> nameColumn = new TableColumn<>("Name");
        nameColumn.setCellValueFactory(new PropertyValueFactory<>("name"));
        nameColumn.setStyle("-fx-text-fill: #38BDF8;");

        TableColumn<Item, String> categoryColumn = new TableColumn<>("Category");
        categoryColumn.setCellValueFactory(new PropertyValueFactory<>("category"));
        categoryColumn.setStyle("-fx-text-fill: #38BDF8;");

        TableColumn<Item, Integer> stockColumn = new TableColumn<>("Stock");
        stockColumn.setCellValueFactory(new PropertyValueFactory<>("stockLevel"));
        stockColumn.setStyle("-fx-text-fill: #38BDF8;");

        TableColumn<Item, Double> priceColumn = new TableColumn<>("Selling Price");
        priceColumn.setCellValueFactory(new PropertyValueFactory<>("sellingPrice"));
        priceColumn.setStyle("-fx-text-fill: #38BDF8;");

        productTable.getColumns().addAll(nameColumn, categoryColumn, stockColumn, priceColumn);

        ObservableList<Item> products = FXCollections.observableArrayList(
                manager.getInventory().getItems()
        );
        productTable.setItems(products);

        VBox layout = new VBox(productTable);
        layout.setPadding(new Insets(20));
        layout.setStyle("-fx-background-color: transparent;");

        root.setTop(header);
        root.setCenter(layout);

        Scene scene = new Scene(root, 1000,
                Screen.getPrimary().getBounds().getHeight());
        productStage.setScene(scene);
        productStage.setTitle("Product Display");
        productStage.show();
    }

    private void signOut(Stage currentStage) {

        Alert alert = new Alert(Alert.AlertType.CONFIRMATION);
        alert.setTitle("Sign Out");
        alert.setHeaderText("Are you sure you want to sign out?");
        alert.setContentText("You will be redirected to the login screen.");

        DialogPane pane = alert.getDialogPane();


        pane.setStyle("""
        -fx-background-color: linear-gradient(
            to bottom right,
            #020617,
            #0F172A
        );
        -fx-border-color: #00B4D8;
        -fx-border-width: 1;
        -fx-border-radius: 10;
        -fx-background-radius: 10;
    """);

        // Header text
        pane.lookup(".header-panel").setStyle("""
        -fx-background-color: transparent;
        -fx-text-fill: #4DF0F8;
    """);

        // Content text
        pane.lookup(".content").setStyle("""
        -fx-text-fill: #E0F7FA;
        -fx-font-size: 13px;
    """);

        // Buttons
        Button okButton = (Button) pane.lookupButton(ButtonType.OK);
        okButton.setStyle("""
        -fx-background-color: linear-gradient(to right, #4CAF50, #2E7D32);
        -fx-text-fill: white;
        -fx-font-weight: bold;
        -fx-background-radius: 8;
        -fx-padding: 6 20;
        -fx-cursor: hand;
    """);

        Button cancelButton = (Button) pane.lookupButton(ButtonType.CANCEL);
        cancelButton.setStyle("""
        -fx-background-color: linear-gradient(to right, #757575, #424242);
        -fx-text-fill: white;
        -fx-font-weight: bold;
        -fx-background-radius: 8;
        -fx-padding: 6 20;
        -fx-cursor: hand;
    """);

        alert.showAndWait().ifPresent(response -> {
            if (response == ButtonType.OK) {
                currentStage.close();
                openLoginScreen();
            }
        });
    }

    private void openLoginScreen() {
        try {
            Stage loginStage = new Stage();
            new org.university.electronic.suppliesmanagementsystem.Controllers.LoginController()
                    .start(loginStage);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }



    private TextField createStyledTextField(String prompt) {
        TextField textField = new TextField();
        textField.setPromptText(prompt);
        textField.setStyle("""
                -fx-background-color: rgba(255,255,255,0.95);
                -fx-background-radius: 10;
                -fx-padding: 10 15;
                -fx-border-color: #00B4D8;
                -fx-border-radius: 10;
                -fx-border-width: 1;
                """);
        textField.setMaxWidth(500);
        textField.setAlignment(Pos.CENTER);
        return textField;
    }
    private void refreshSidebarStats() {
        totalItemsValueLabel.setText(
                String.valueOf(manager.getInventory().getItems().size())
        );

        lowStockValueLabel.setText(
                String.valueOf(manager.getInventory().getLowStockItems(5).size())
        );

        suppliersValueLabel.setText(
                String.valueOf(
                        manager.getSuppliers() != null
                                ? manager.getSuppliers().size()
                                : 0
                )
        );
    }


    private void showStyledAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);

        DialogPane dialogPane = alert.getDialogPane();
        dialogPane.setStyle("""
                -fx-background-color: linear-gradient(
                    to bottom right,
                    #020617,
                    #0F172A
                );
                -fx-text-fill: white;
                """);

        alert.showAndWait();
    }
}