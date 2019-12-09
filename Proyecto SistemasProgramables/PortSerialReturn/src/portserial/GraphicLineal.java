/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package portserial;

import gnu.io.CommPortIdentifier;
import gnu.io.PortInUseException;
import gnu.io.SerialPort;
import gnu.io.UnsupportedCommOperationException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Enumeration;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.JOptionPane;

import org.jfree.data.xy.XYSeries;
import org.jfree.data.xy.XYSeriesCollection;
import org.jfree.chart.JFreeChart;
import org.jfree.chart.plot.PlotOrientation;
import org.jfree.chart.plot.XYPlot;
import org.jfree.chart.ChartFactory;
import org.jfree.chart.ChartPanel;
import org.jfree.chart.renderer.xy.XYLineAndShapeRenderer;
import org.jfree.chart.ChartPanel;

import java.awt.Color;
import java.awt.BasicStroke;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.util.ArrayList;
import javax.swing.JFrame;
import org.jfree.data.category.DefaultCategoryDataset;
import org.jfree.data.xy.XYDataset;

/**
 *
 * @author isaac
 */
public class GraphicLineal {
    ConnectionBdd connection = new ConnectionBdd();
    JFreeChart xylineChart;
    DefaultCategoryDataset dataset = new DefaultCategoryDataset();
    XYSeries channelOne = new XYSeries("Channel One");
    XYSeries channelTwo = new XYSeries("Channel Two");
    XYSeries channelTree = new XYSeries("Channel Tree");
    XYSeries channelFour = new XYSeries("Channel Four");

    public GraphicLineal() {
        xylineChart = ChartFactory.createLineChart(
                "Lineal Graphic",
                "Time",
                "Volts",
                dataset,
                PlotOrientation.VERTICAL, true, true, true);
    }

    public void addValues(Double volts1[], Double volts2[], Double volts3[], Double volts4[], int x, float time[]) {
        
        for (int i = 0; i < volts1.length; i++) {
            dataset.addValue(volts1[i], "channelOne", "" + i);
            System.out.println("valor del vector " + volts1[i]);
            dataset.addValue(volts2[i], "channelTwo", "" + i);
             System.out.println("valor del vector " + volts2[i]);
            dataset.addValue(volts3[i], "channelThree", "" + i);
            dataset.addValue(volts4[i], "channelFour", "" + i);
        }
    }

    public void addValuesChannels(String channel, float[] volts, float[] time, int x) {
        System.out.println("channel " + channel);
        ConnectionBdd connect = new ConnectionBdd();
        connect.conectar();
        switch (channel) {
            case "channelOne":
                for (int i = 0; i < volts.length; i++) {
                    dataset.addValue(volts[i], "channelOne", "" + x);
                    String sql = "insert into channelOne values(null," + volts[i] + "," + x + ",now())";
                    System.out.println("valor del vector " + volts[i]);
                }
                break;
            case "channelTwo":
                for (int i = 0; i < volts.length; i++) {
                    dataset.addValue(volts[i], "channelTwo", "" + x);
                }
                break;
            case "channelTree":
                for (int i = 0; i < volts.length; i++) {
                    channelTree.add(time[i], volts[i]);
                }
                break;
            case "channelFour":
                for (int i = 0; i < volts.length; i++) {
                    channelFour.add(time[i], volts[i]);
                }
                break;
        }
    }

    public void render() {
        /*
        dataset.addSeries(channelOne);
        dataset.addSeries(channelTwo);
        dataset.addSeries(channelTree);
        dataset.addSeries(channelFour);
        xylineChart = ChartFactory.createXYLineChart(
                "Grafica XY",
                "Time",
                "Volts",
                dataset,
                PlotOrientation.VERTICAL, true, true, true);

        XYPlot plot = xylineChart.getXYPlot();

        XYLineAndShapeRenderer renderer = new XYLineAndShapeRenderer();
        renderer.setSeriesPaint(0, Color.RED);
        renderer.setSeriesPaint(1, Color.GREEN);
        renderer.setSeriesPaint(2, Color.YELLOW);
        renderer.setSeriesPaint(3, Color.DARK_GRAY);
        renderer.setSeriesStroke(0, new BasicStroke(4.0f));
        renderer.setSeriesStroke(1, new BasicStroke(3.0f));
        renderer.setSeriesStroke(2, new BasicStroke(2.0f));
        renderer.setSeriesStroke(3, new BasicStroke(2.0f));
        plot.setRenderer(renderer);*/
    }

    public void insertValues(float volts1[], float volts2[], float volts3[], float volts4[], int x) {
        Connection con = connection.conectar();
        for (int i = 0; i < volts1.length; i++) {
            String sql = "insert into channelOne values(null," + volts1[i] + "," + x + ",now())";
            try {
                PreparedStatement state = con.prepareStatement(sql);
                state.executeUpdate();
                con.close();
            } catch (SQLException ex) {
                Logger.getLogger(MainPortSerial.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
        for (int i = 0; i < volts2.length; i++) {
             String sql = "insert into channelTwo values(null," + volts2[i] + "," + x + ",now())";
            try {
                PreparedStatement state = con.prepareStatement(sql);
                state.executeUpdate();
                con.close();
            } catch (SQLException ex) {
                Logger.getLogger(MainPortSerial.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
        for (int i = 0; i < volts3.length; i++) {
            String sql = "insert into channelThree values(null," + volts3[i] + "," + x + ",now())";
            try {
                PreparedStatement state = con.prepareStatement(sql);
                state.executeUpdate();
                con.close();
            } catch (SQLException ex) {
                Logger.getLogger(MainPortSerial.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
        for (int i = 0; i < volts4.length; i++) {
            String sql = "insert into channelFour values(null," + volts4[i] + "," + x + ",now())";
            try {
                PreparedStatement state = con.prepareStatement(sql);
                state.executeUpdate();
                con.close();
            } catch (SQLException ex) {
                Logger.getLogger(MainPortSerial.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
    }

    public ChartPanel getPanel() {
        ChartPanel panel = new ChartPanel(xylineChart);
        return panel;
    }

}
