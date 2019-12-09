/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package portserial;

import gnu.io.CommPortIdentifier;
import java.util.Enumeration;

/**
 *
 * @author isaac
 */
public class PortSerial {

    /**
     * @param args the command line arguments
     */
    

    public static void main(String[] args) {
        ConnectionBdd connection = new ConnectionBdd();
        connection.conectar();
        /*
        conocer elpath de java
        String libPathProperty = System.getProperty("java.library.path");
        System.out.println(libPathProperty);
         */
    }

}
