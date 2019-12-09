/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package portserial;

/**
 *
 * @author isaac
 */
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.logging.Level;
import java.util.logging.Logger;
public class ConnectionBdd {
    Connection connection = null;
    
     public  Connection conectar(){
        try {
            Class.forName("com.mysql.jdbc.Driver");
            connection = DriverManager.getConnection("jdbc:mysql://localhost/pic","root","1234");
            System.out.println("connected");
        } catch (ClassNotFoundException ex) {
            System.out.println("error q "+ ex.getMessage());
        } catch (SQLException ex) {
            System.out.println("error j "+ ex.getMessage());
        }
        return connection;
    }
}
