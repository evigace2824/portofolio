package org.university.electronic.suppliesmanagementsystem.Models;

import java.util.ArrayList;
import java.util.List;

public class Supplier {
    private String name; //suppliers name
    private String contactInfo; //contact details
    private List<String> products; //list of product categories/items the supplier provides

    public Supplier(String name, String contactInfo) {
        this.name = name;
        this.contactInfo = contactInfo;
        this.products = new ArrayList<>();
    }

    //adds a product to the suppliers list
    public void addProduct(String product) {
        if (!products.contains(product)) {
            products.add(product);
            System.out.println("Product added to supplier: " + product);
        } else {
            System.out.println("Product already exists for supplier: " + product);
        }
    }

    //removes a product from the suppliers list
    public void removeProduct(String product) {
        if (products.remove(product)) {
            System.out.println("Product removed from supplier: " + product);
        } else {
            System.out.println("Product not found for supplier: " + product);
        }
    }

    //gets the list of products offered by the supplier
    public List<String> getProducts() {
        return products;
    }

    //gets supplier name
    public String getName() {
        return name;
    }

    //gets contact information
    public String getContactInfo() {
        return contactInfo;
    }

    //sets supplier name
    public void setName(String name) {
        this.name = name;
    }

    //sets contact information
    public void setContactInfo(String contactInfo) {
        this.contactInfo = contactInfo;
    }

    @Override
    public String toString() {
        return "Supplier{" +
                "name='" + name + '\'' +
                ", contactInfo='" + contactInfo + '\'' +
                ", products=" + products +
                '}';
    }
}
