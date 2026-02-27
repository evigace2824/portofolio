package org.university.electronic.suppliesmanagementsystem.Models;

public class Item {
    private String name;
    private String category;  
    private double purchasePrice; 
    private double sellingPrice;
    private int stockLevel;

    public Item(String name, String category, double purchasePrice, double sellingPrice, int stockLevel) {
        this.name = name;
        this.category = category;
        this.purchasePrice = purchasePrice;
        this.sellingPrice = sellingPrice;
        this.stockLevel = stockLevel;
    }


	//getters and setters for all attributes
    public String getName() {
        return name;
    }

    public String getCategory() {
        return category;
    }

    public double getPurchasePrice() {
        return purchasePrice;
    }

    public double getSellingPrice() {
        return sellingPrice;
    }

    public int getStockLevel() {
        return stockLevel;
    }

    public void setStockLevel(int stockLevel) {
        this.stockLevel = stockLevel;
    }

    //converts item to a string representation
    @Override
    public String toString() {
        return name + " (" + category + ") - Purchase Price: $" + purchasePrice + ", Selling Price: $" + sellingPrice + ", Stock: " + stockLevel;
    }

	public void setSellingPrice(double sellingPrice) {
		this.sellingPrice = sellingPrice;
		
	}
}
