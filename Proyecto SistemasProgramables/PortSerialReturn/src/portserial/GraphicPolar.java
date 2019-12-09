/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package portserial;

import java.awt.BasicStroke;
import java.awt.Color;
import org.jfree.chart.ChartFactory;
import org.jfree.chart.ChartPanel;
import org.jfree.chart.JFreeChart;
import org.jfree.chart.plot.PlotOrientation;
import org.jfree.chart.plot.XYPlot;
import org.jfree.chart.renderer.xy.XYLineAndShapeRenderer;
import org.jfree.data.xy.XYSeries;
import org.jfree.data.xy.XYSeriesCollection;

/**
 *
 * @author isaac
 */
public class GraphicPolar {

    JFreeChart chartPolar;
    XYSeriesCollection dataset = new XYSeriesCollection();
    boolean flagChannelOne = false;
    boolean flagChannelTwo = false;
    boolean flagChannelTree = false;
    boolean flagChannelFour = false;

    GraphicPolar() {
        chartPolar = ChartFactory.createPolarChart(
                "Polar Graphic", // Chart title
                dataset,
                true,
                true,
                true
        );
    }

    public void addValuesPolar(Double[] volts1, Double[] volts2, Double[] volts3, Double[] volts4, int x) {
        XYSeries channelOne = new XYSeries("Channel One");
        XYSeries channelTwo = new XYSeries("Channel Two");
        XYSeries channelTree = new XYSeries("Channel Tree");
        XYSeries channelFour = new XYSeries("Channel Four");
        if (volts1[0]!=null) {
            double grados = 0;
            for (int i = 0; i <= 8; i++) {
                double radio = Math.sqrt(Math.pow(volts1[i], 2) + Math.pow(i, 2));
//                double angulo = (volts1[i]/i);
//                double arctangente = Math.atan(angulo);
//                double grados = Math.toDegrees(arctangente);
                channelOne.add((int)grados,radio);
                grados = grados + 45;
            }
            dataset.addSeries(channelOne);
        }
           System.out.println("vec2 "+volts2.length);
        if (volts2[0]!=null) {
            double grados = 0;
            for (int i = 0; i <= 8; i++) {
                double radio = Math.sqrt((Math.pow(volts2[i], 2)) + (Math.pow(i, 2)));
                channelTwo.add((int)grados,radio);
                grados = grados + 45;
            }
//            for (int i = 1; i < 11; i++) {                
//                double radio = Math.sqrt(Math.pow(volts2[i-1], 2) + Math.pow(x, 2));
//                double angulo = (volts2[i-1]/i);
//                double arctangente = Math.atan(angulo);
//                double grados = Math.toDegrees(arctangente);
//                //System.out.println("radio ->" + radio + "grados ->" + grados);
//                channelTwo.add((int) grados, volts2[i-1]);
//            }
            dataset.addSeries(channelTwo);
        }

        if (volts3[0]!=null) {
            double grados = 0;
            for (int i = 0; i <= 8; i++) {
                double radio = Math.sqrt((Math.pow(volts3[i], 2)) + (Math.pow(i, 2)));
                channelTree.add((int)grados,radio);
                grados = grados + 45;
            }
//            for (int i = 1; i < 11; i++) {
//                double radio = Math.sqrt(Math.pow(volts3[i-1], 2) + Math.pow(x, 2));
//                double angulo = (volts3[i-1]/i);
//                double arctangente = Math.atan(angulo);
//                double grados = Math.toDegrees(arctangente);
//                //System.out.println("radio ->" + radio + "grados ->" + grados);
//                channelTree.add((int) grados, volts3[i-1]);
//            }
            dataset.addSeries(channelTree);
        }
        if (volts4[0]!=null) {
            double grados = 0;
            for (int i = 0; i <= 8; i++) {
                double radio = Math.sqrt((Math.pow(volts4[i], 2)) + (Math.pow(i, 2)));
                channelFour.add((int)grados,radio);
                grados = grados + 45;
            }
//            for (int i = 1; i < 11; i++) {
//                double radio = Math.sqrt(Math.pow(volts4[i-1], 2) + Math.pow(x, 2));
//                double angulo = (volts4[i-1]/i);
//                double arctangente = Math.atan(angulo);
//                double grados = Math.toDegrees(arctangente);
//                //System.out.println("radio ->" + radio + "grados ->" + grados);
//                channelFour.add((int) grados, volts4[i-1]);
//            }
            dataset.addSeries(channelFour);
        }

    }

    public void addValuesChannelsPolar(String channel, float[] volts, float[] time, int x) {

        XYSeries channelTwo = new XYSeries("Channel Two");
        XYSeries channelTree = new XYSeries("Channel Tree");
        XYSeries channelFour = new XYSeries("Channel Four");
        System.out.println("channel " + channel);
        switch (channel) {
            case "channelOne":
                XYSeries channelOne = new XYSeries("Channel One");
                for (int i = 0; i < volts.length; i++) {
                    double radio = Math.sqrt(Math.pow(volts[i], 2) + Math.pow(x, 2));
                    double angulo = (i / volts[i]);
                    double arctangente = Math.atan(angulo);
                    double grados = Math.toDegrees(arctangente);
                    System.out.println("radio ->" + radio + "grados ->" + grados);
                    channelOne.add((int) grados, volts[i]);
                }
                dataset.addSeries(channelOne);
                break;
            case "channelTwo":
                for (int i = 0; i < volts.length; i++) {
                    double radio = Math.sqrt(Math.pow(volts[i], 2) + Math.pow(time[i], 2));
                    double angulo = (time[i] / volts[i]);
                    double arctangente = Math.atan(angulo);
                    double grados = Math.toDegrees(arctangente);
                    System.out.println("radio ->" + radio + "grados ->" + grados);
                    channelTwo.add(radio, grados);
                }
                break;
            case "channelTree":
                for (int i = 0; i < volts.length; i++) {
                    double radio = Math.sqrt(Math.pow(volts[i], 2) + Math.pow(time[i], 2));
                    double angulo = (time[i] / volts[i]);
                    double arctangente = Math.atan(angulo);
                    double grados = Math.toDegrees(arctangente);
                    System.out.println("radio ->" + radio + "grados ->" + grados);
                    channelTree.add(radio, grados);
                }
                break;
            case "channelFour":
                for (int i = 0; i < volts.length; i++) {
                    double radio = Math.sqrt(Math.pow(volts[i], 2) + Math.pow(time[i], 2));
                    double angulo = (time[i] / volts[i]);
                    double arctangente = Math.atan(angulo);
                    double grados = Math.toDegrees(arctangente);
                    System.out.println("radio ->" + radio + "grados ->" + grados);
                    channelFour.add(radio, grados);
                }
                break;
        }
    }

    /*
    public void habilitarChannel(String channel, boolean flag) {
        switch (channel) {
            case "channelOne":
                flagChannelOne = flag;
                dataset.addSeries(channelOne);
                break;
            case "channelTwo":
                flagChannelTwo = flag;
                dataset.addSeries(channelTwo);
                break;
            case "channelTree":
                flagChannelTree = flag;
                dataset.addSeries(channelTree);
                break;
            case "channelFour":
                flagChannelFour = flag;
                dataset.addSeries(channelFour);
                break;
        }
    }
     */
    public void renderPolar() {
        chartPolar = ChartFactory.createPolarChart(
                "Polar Graphic", // Chart title
                dataset,
                true,
                true,
                false
        );

    }

    public ChartPanel getPanelPolar() {
        ChartPanel panel = new ChartPanel(chartPolar);

        return panel;
    }

}
